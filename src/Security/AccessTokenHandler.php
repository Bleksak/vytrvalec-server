<?php

declare(strict_types=1);

namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        #[SensitiveParameter]
        private string $secret,
    ) {}

    #[\Override]
    public function getUserBadgeFrom(
        #[SensitiveParameter] string $accessToken,
    ): UserBadge {
        try {
            $payload = JWT::decode(
                $accessToken,
                new Key($this->secret, 'HS256'),
            );

            $payload = $this->denormalizer->denormalize(
                $payload,
                JWTPayload::class,
            );

            return new UserBadge($payload->user);
        } catch (\Throwable) {
            throw new BadCredentialsException();
        }
    }
}
