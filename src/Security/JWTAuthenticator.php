<?php

namespace App\Security;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JWTAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly UserProviderInterface $userProvider)
    {
    }

    public function supports(Request $request): ?bool
    {
        // if using isGranted properly, this is not a vulnerability
        return $request->cookies->has('jwt') && $request->get('_route') !== 'api_user_login';
    }

    public function authenticate(Request $request): Passport
    {
        $jwt = $request->cookies->get('jwt');

        try {
            $payload = JWT::decode($jwt, new Key($request->server->get('JWT_SECRET'), 'HS256'));
            $email = $payload->user;

            return new SelfValidatingPassport(
                new UserBadge($email, function($email) {
                    return $this->userProvider->loadUserByIdentifier($email);
                })
            );

        } catch(ExpiredException) {
            throw new CustomUserMessageAuthenticationException("session_expired");
        } catch(SignatureInvalidException) {
            // delete the invalid token
            setcookie('jwt', '', time() - 1, path: '/', secure: $request->isSecure());
            throw new CustomUserMessageAuthenticationException("bad_token");
        } catch(Exception $e) {
            throw new CustomUserMessageAuthenticationException();
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'success' => false,
            'error' => $exception->getMessageKey(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
