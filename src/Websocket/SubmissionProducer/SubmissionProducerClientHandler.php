<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientGateway;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\Server\WebsocketGateway;
use Amp\Websocket\WebsocketClient;
use App\Entity\Submission;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Security\AccessTokenHandler;
use App\Services\ImagePath;
use App\Sync\ReentrantMutex;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class SubmissionProducerClientHandler implements WebsocketClientHandler
{
    private const int SUBMISSIONS_BUFFER_LIMIT = 2;
    /**
     * @var array<int, Submission>
     */
    private array $submissions = [];
    private ReentrantMutex $submissionsMutex;

    /**
     * Contains the ids of free submissions.
     *
     * @var array<int>
     */
    private array $freeList = [];
    private ReentrantMutex $freeListMutex;

    /**
     * @var array<int, SubmissionProducerClient>
     */
    private array $clients = [];

    private readonly WebsocketGateway $gateway;

    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly UserRepository $userRepository,
        private readonly AccessTokenHandler $accessTokenHandler,
        private readonly SerializerInterface $serializer,
        private readonly DenormalizerInterface $denormalizer,
        private readonly ImagePath $imagePath,
    ) {
        $this->submissionsMutex = new ReentrantMutex();
        $this->freeListMutex = new ReentrantMutex();
        $this->gateway = new WebsocketClientGateway();
        $this->refetch();
    }

    private function refetch(): void
    {
        echo 'Refetching submissions'.PHP_EOL;

        echo 'Locking submission'.PHP_EOL;
        $submissionsLock = $this->submissionsMutex->acquire();
        echo 'Locking free-list'.PHP_EOL;
        $freeListLock = $this->freeListMutex->acquire();

        $oldSubmissions = array_map(
            fn (Submission $submission): int => $submission->getId(),
            $this->submissions,
        );

        $ignoredIds = array_diff($oldSubmissions, $this->freeList);

        $newSubmissions = $this->submissionRepository->findUnreviewed(self::SUBMISSIONS_BUFFER_LIMIT, $ignoredIds);

        $toMerge = array_filter(
            array_diff_key($newSubmissions, $this->submissions),
            fn (Submission $submission): bool => $submission->isReviewed() === false
        );

        foreach ($toMerge as $key => $submission) {
            $this->submissions[$key] = $submission;
        }

        $this->freeList += array_keys($newSubmissions);

        echo 'Releasing free-list'.PHP_EOL;
        $freeListLock->release();
        echo 'Releasing submissions'.PHP_EOL;
        $submissionsLock->release();
    }

    private function triggerDisconnect(WebsocketClient $client): void
    {
        if (!isset($this->clients[$client->getId()])) {
            return;
        }

        echo sprintf('Releasing client id %d'.PHP_EOL, $client->getId());

        $submissionId = $this->clients[$client->getId()]->submissionId;

        if ($submissionId !== null) {
            $lock = $this->freeListMutex->acquire();
            $this->freeList[] = $submissionId;
            $lock->release();
        }

        unset($this->clients[$client->getId()]);
        $client->close();
    }

    #[\Override]
    public function handleClient(
        WebsocketClient $client,
        Request $request,
        Response $response,
    ): void {
        $this->gateway->addClient($client);

        $customClient = new SubmissionProducerClient($client->getId());
        $this->clients[$client->getId()] = $customClient;

        echo sprintf('New client with id %d joined'.PHP_EOL, $client->getId());

        try {
            while ($message = $client->receive()) {
                $buffer = $message->buffer();
                /**
                 * @var SubmissionProducerMessage
                 */
                $message = $this->serializer->deserialize($buffer, SubmissionProducerMessage::class, 'json');

                echo sprintf('Client: %d - Got message of type: %s'.PHP_EOL, $client->getId(), $message->type->value);

                if ($message->type !== SubmissionProducerMessageType::InitializeRequest && $customClient->user === null) {
                    $client->sendText(
                        $this->serializer->serialize(
                            new SubmissionProducerMessage(
                                SubmissionProducerMessageType::Fail,
                                SubmissionProducerMessageType::SubmissionReviewRequest,
                                [
                                    'message' => 'Uninitialized connection, send an initialize request first',
                                ]
                            ),
                            'json',
                        )
                    );

                    $this->triggerDisconnect($client);

                    return;
                }

                switch ($message->type) {
                    case SubmissionProducerMessageType::InitializeRequest:
                        $payload = $this->denormalizer->denormalize($message->payload, SubmissionProducerInitializeRequestPayload::class);

                        $user = SubmissionProducerMessage::handleInitialize(
                            $payload,
                            $this->accessTokenHandler,
                            $this->userRepository
                        );

                        if ($user === null) {
                            $client->sendText(
                                $this->serializer->serialize(
                                    new SubmissionProducerMessage(
                                        SubmissionProducerMessageType::Fail,
                                        SubmissionProducerMessageType::SubmissionReviewRequest,
                                        [
                                            'message' => 'Invalid JWT token',
                                        ]
                                    ),
                                    'json',
                                )
                            );

                            $this->triggerDisconnect($client);

                            return;
                        }

                        $customClient->user = $user;

                        $client->sendText(
                            $this->serializer->serialize(
                                new SubmissionProducerMessage(
                                    SubmissionProducerMessageType::Success,
                                    SubmissionProducerMessageType::InitializeRequest
                                ),
                                'json',
                            )
                        );

                        break;
                    case SubmissionProducerMessageType::SubmissionRequest:
                        if ($customClient->submissionId === null) {
                            $customClient->submissionId = $this->allocateSubmission();
                        }

                        if ($customClient->submissionId === null) {
                            echo 'No more submissions to process'.PHP_EOL;
                            $client->sendText(
                                $this->serializer->serialize(
                                    new SubmissionProducerMessage(
                                        SubmissionProducerMessageType::Fail,
                                        SubmissionProducerMessageType::SubmissionRequest
                                    ),
                                    'json'
                                )
                            );

                            $this->triggerDisconnect($client);

                            return;
                        }

                        $submission = $this->submissions[$customClient->submissionId];
                        $user = $submission->getUser();

                        $response = new SubmissionProducerMessage(
                            SubmissionProducerMessageType::Success,
                            SubmissionProducerMessageType::SubmissionRequest,
                            [
                                'submission' => $submission->toResponseObject($this->imagePath),
                                'user' => $user->toResponseObject(),
                            ],
                        );

                        $client->sendText(
                            $this->serializer->serialize(
                                $response,
                                'json',
                            )
                        );

                        break;
                    case SubmissionProducerMessageType::SubmissionReviewRequest:
                        if ($customClient->submissionId === null) {
                            $client->sendText(
                                $this->serializer->serialize(
                                    new SubmissionProducerMessage(
                                        SubmissionProducerMessageType::Fail,
                                        SubmissionProducerMessageType::SubmissionReviewRequest,
                                        [
                                            'message' => 'Client has no submission to be reviewed.',
                                        ]
                                    ),
                                    'json',
                                )
                            );

                            $this->triggerDisconnect($client);

                            return;
                        }

                        /**
                         * @var SubmissionProducerReviewPayload
                         */
                        $payload = $this->denormalizer->denormalize($message->payload, SubmissionProducerReviewPayload::class);

                        $submission = $this->submissions[$customClient->submissionId];

                        $submission->setReviewed(true);
                        $submission->setAccepted($payload->accepted);

                        $this->submissionRepository->save($submission, true);

                        $customClient->submissionId = null;

                        $client->sendText(
                            $this->serializer->serialize(
                                new SubmissionProducerMessage(
                                    SubmissionProducerMessageType::Success,
                                    SubmissionProducerMessageType::SubmissionReviewRequest,
                                ),
                                'json',
                            )
                        );

                        break;
                }
            }
        } catch (\Throwable $e) {
            echo $e->getLine().PHP_EOL;
            echo $e->getMessage().PHP_EOL;
        } finally {
            $this->triggerDisconnect($client);
        }
    }

    private function allocateSubmission(): ?int
    {
        echo 'Allocating submission'.PHP_EOL;
        echo 'Locking free-list'.PHP_EOL;

        $lock = $this->freeListMutex->acquire();

        if ($this->freeList === []) {
            $this->refetch();
        }

        if ($this->freeList === []) {
            return null;
        }

        $submissionId = array_pop($this->freeList);

        echo 'Releasing free-list'.PHP_EOL;
        $lock->release();

        return $submissionId;
    }
}
