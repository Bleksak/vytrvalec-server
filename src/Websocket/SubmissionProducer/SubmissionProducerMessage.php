<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\FeatureFlag;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;

final class SubmissionProducerMessage
{
    public function __construct(
        public SubmissionProducerMessageType $type,
        public ?SubmissionProducerMessageType $responseTo = null,
        public mixed $payload = null,
    ) {
    }

    public static function handleInitialize(
        SubmissionProducerInitializeRequestPayload $payload,
        AccessTokenHandlerInterface $accessTokenHandler,
        UserRepository $userRepository,
    ): ?User {
        try {
            $userBadge = $accessTokenHandler->getUserBadgeFrom($payload->jwt);
            $email = $userBadge->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
        } catch (AuthenticationException) {
            return null;
        }

        if ($user === null) {
            return null;
        }

        if (!$user->canAccess(FeatureFlag::ROLE_STAFF)) {
            return null;
        }

        return $user;
    }
}
