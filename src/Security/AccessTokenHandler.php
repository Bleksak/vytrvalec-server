<?php

declare(strict_types=1);

namespace App\Security;

use Composer\Semver\Semver;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public const string VERSION = '1.0.0';

    public function __construct(
        private DenormalizerInterface $denormalizer,
        #[SensitiveParameter]
        private string $secret,
    ) {}

    /**
     * @throws BadCredentialsException|AuthenticationExpiredException
     */
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

            $currentTimestamp = new \DateTime()->getTimestamp();

            if ($payload->exp < $currentTimestamp) {
                throw new AuthenticationExpiredException();
            }

            if (!Semver::satisfies($payload->version, '^' . self::VERSION)) {
                throw new AuthenticationExpiredException();
            }

            return new UserBadge($payload->user);
        } catch (\Throwable) {
            throw new BadCredentialsException();
        }
    }
}
