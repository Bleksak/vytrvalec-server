<?php

declare(strict_types=1);

namespace App\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private ParameterBagInterface $parameters,
    ) {
    }

    #[\Override]
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $payload = JWT::decode(
                $accessToken,
                new Key($this->parameters->get('jwt_secret'), 'HS256')
            );

            return new UserBadge($payload->user);
        } catch (\Throwable) {
            setcookie('jwt', '', time() - 1, path: '/', httponly: true);
            throw new BadCredentialsException();
        }
    }
}
