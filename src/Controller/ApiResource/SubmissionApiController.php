<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\RejectedSubmissionMessage;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Notifications\Firebase\Firebase;
use App\Notifications\Firebase\FirebaseNotification;
use App\Repository\RejectedSubmissionMessageRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Requests\SubmissionRequest;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Imagick;
use ImagickException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Submission')]
class SubmissionApiController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly SubmissionRepository $submissionRepository, private readonly SerializerInterface $serializer)
    {
    }

    #[ApiRoute(
        '/api/submission/create',
        name: 'api_submission_create',
        methods: ['POST'],
        documentation: 'Creates a new <code>Submission</code> entity',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Submission created successfully',
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request ',
                'response' => [
                    'distance' => 'err_negative_value',
                    'elevation' => 'err_zero_value'
                ]
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR => [
                'message' => 'Error when processing image'
            ]
        ],
        requestScheme: [
            'distance' => 'integer',
            'elevation' => 'integer',
            'image' => 'file',
            'activity' => 'integer'
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function create(#[CurrentUser] User $user, SubmissionRequest $request, Request $httpRequest, SeasonRepository $seasonRepository): Response
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $submission = new Submission();
        $season = $seasonRepository->getCurrent();

        if (!$season) {
            return $this->json(['no_season'], Response::HTTP_BAD_REQUEST);
        }

        $submission->setSeason($season);
        $submission->setElevation($request->getElevation());
        $submission->setDistance($request->getDistance());

        $submission->setUser($user);
        $submission->setActivity($request->getActivity());
        $submission->setReviewed(false);
        $submission->setAccepted(false);
        $submission->setDate(new DateTimeImmutable());
        $submission->setFaculty($user->getFaculty());
        $submission->calculateWeek();

        $uniquePath = uniqid('/uploads/') . '.jpg';
        $absolutePath = $httpRequest->server->get('DOCUMENT_ROOT') . $uniquePath;

        try {
            $img = new Imagick($request->getImage()->getRealPath());
            $profiles = $img->getImageProfiles("icc");

            $img->stripImage();
            if (!empty($profiles)) {
                $img->profileImage("icc", $profiles['icc']);
            }

            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (ImagickException) {
            return new Response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $submission->setImage($uniquePath);

        $this->em->persist($submission);
        $this->em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/submission/list/{season}/{page}',
        name: 'api_submission_list_season',
        methods: ['GET'],
        documentation: 'Retrieves all submissions in given Season',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all submissions',
                'response' => [
                    'pages' => 'integer',
                    'submissions' => 'array',
                    'users' => 'array'
                ]
            ]
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function listSeason(Season $season, int $page): Response
    {
        $limit = 50;
        $users = $this->submissionRepository->findUsersBySeason($season, $page, $limit);
        $submissions = $this->submissionRepository->findBySeason($season, $page, $limit);
        $pageCount = 1 + intdiv($submissions->count(), $limit);

        return $this->json(
            $this->serializer->normalize(
                [
                    'pages' => $pageCount,
                    'submissions' => $submissions,
                    'users' => $users,
                ],
                null,
                [
                    AbstractNormalizer::GROUPS => ['fetchSubmission'],
                    AbstractNormalizer::CALLBACKS => [
                        'season' => fn($object) => $object->getId(),
                        'activity' => fn($object) => $object->getId(),
                        'user' => fn($object) => $object->getId(),
                        'faculty' => fn($object) => $object->getId(),
                    ],
                ]
            )
        );
    }

    #[ApiRoute(
        '/api/submission/user/list/{page}/{limit}',
        name: 'api_submission_list',
        methods: ['GET'],
        documentation: 'Retrieves all submissions for current user',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all submissions'
            ]
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function list(#[CurrentUser] User $user, RejectedSubmissionMessageRepository $rejectedSubmissionMessageRepository, int $page, int $limit = 50) : Response
    {
        $rejectedSubmissions = $rejectedSubmissionMessageRepository->findByUser($user);
        $submissions = $this->submissionRepository->findAllByUser($user, $page, $limit);
        $pageCount = 1 + intdiv($submissions->count(), $limit);
        $nextPage = ($page + 1) > $pageCount ? null : $page + 1;

        return $this->json($this->serializer->normalize(['nextPage' => $nextPage, 'submissions' => $submissions, 'rejectedSubmissions' => $rejectedSubmissions], null, [
            AbstractNormalizer::GROUPS => ['fetchSubmission'],
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['faculty', 'user'],
            AbstractNormalizer::CALLBACKS => [
                'season' => fn($object) => $object->getId(),
                'activity' => fn($object) => $object->getId(),
            ]
        ]));
    }

    #[ApiRoute(
        '/api/submission/unresolved/{season}',
        name: 'api_submission_list_unresolved',
        methods: ['GET'],
        documentation: 'Retrieves all unresolved submissions in the given season',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all unresolved submissions'
            ]
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function unresolvedList(Season $season): Response
    {
        return $this->json($this->serializer->normalize($this->submissionRepository->findBy(['season' => $season, 'reviewed' => false]), null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['fetchSubmission'],
        ]));
    }

    #[ApiRoute(
        '/api/submission/{submission}/delete',
        name: 'api_submission_delete',
        methods: ['DELETE'],
        documentation: 'Deletes a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully deleted'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Cannot delete'
            ]
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function delete(#[CurrentUser] User $user, Submission $submission): Response
    {
        if (!$user->hasRole('ROLE_STAFF') && $user !== $submission->getUser()) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        if ($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->em->remove($submission);
        $this->em->flush();

        return new Response(status: Response::HTTP_OK);
    }

    private function setState(Submission $submission, bool $state): void
    {
        $submission->setAccepted($state);
        $submission->setReviewed(true);
    }

    #[ApiRoute(
        '/api/submission/{submission}/accept',
        name: 'api_submission_accept',
        methods: ['PUT'],
        documentation: 'Accepts a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully accepted'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Cannot accept'
            ]
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function accept(Submission $submission): Response
    {
        if ($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->setState($submission, true);
        $this->submissionRepository->save($submission, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/submission/rejected',
        name: 'api_submission_reject',
        methods: ['PUT'],
        documentation: 'Rejects a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully rejected'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
        ],
    )]
    #[IsGranted('ROLE_USER')]
    public function listRejected(#[CurrentUser] User $user): Response
    {
        $this->submissionRepository->findBy(['user' => $user, 'accepted' => false, 'reviewed' => true]);
    }

    #[ApiRoute(
        '/api/submission/{submission}/reject',
        name: 'api_submission_reject',
        methods: ['PUT'],
        documentation: 'Rejects a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully rejected'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Cannot reject'
            ]
        ],
        requestScheme: [
            'message' => 'string'
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function reject(Request $request, Submission $submission, RejectedSubmissionMessageRepository $repository, Firebase $firebase): Response
    {
        if ($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->setState($submission, false);
        $this->submissionRepository->save($submission);

        $message = $request->getPayload()->get('message');
        if (empty($message)) {
            $message = 'Vaše aktivita ze dne ' . $submission->getDate()->format('d. m. Y') . ' byla zamítnuta.\nZkontrolujte prosím zadané údaje a případně je upravte.\n\nDěkujeme za pochopení,\nKatedra tělesné výchovy a sportu ZČU v Plzni';
        }

        $rejectedMessage = (new RejectedSubmissionMessage())->setSubmission($submission)->setMessage($message);
        $repository->save($rejectedMessage, true);


        //        $notification = (new Notification('Měsíční vytrvalec', ['email', 'expo']))->content($message);
//        $recipient = new Recipient($submission->getUser()->getEmail());

        //        $notifier->send($notification, $recipient);
        $firebase->send(new FirebaseNotification($submission->getUser()->getFirebaseToken(), 'Send nudes plz', 'plííííz', 'ASPON_BOOBIEZ?'));

        return new Response(status: Response::HTTP_OK);
    }

    #[Route('/notifyTest', name: 'notification_test', env: 'dev')]
    public function notificationTest(Firebase $firebase): Response
    {
        $firebase->send(new FirebaseNotification('/topics/new_season', 'Send nudes plz', 'plííííz', 'ASPON_BOOBIEZ?'));

        return new Response('OK', 200);
    }
}