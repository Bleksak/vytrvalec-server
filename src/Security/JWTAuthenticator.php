<?php

namespace App\Security;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    public function __construct(private readonly UserProviderInterface $userProvider, private readonly ParameterBagInterface $parameters)
    {
    }

    public function supports(Request $request): ?bool
    {
        // if using isGranted properly, this is not a vulnerability
        return ($request->cookies->has('jwt') || $request->headers->has('Authorization')) && $request->get('_route') !== 'api_user_login';
    }

    private function getHeaderToken(Request $request) : string|null
    {
        $authorization = $request->headers->get('Authorization', null);

        if($authorization === null) {
            return null;
        }

        // When auth token == "Bearer Bearer "
        // then "Bearer " is an invalid jwt token
        $exploded = explode("Bearer ", $authorization);

        if(count($exploded) != 2) {
            throw new CustomUserMessageAuthenticationException();
        }

        [, $token] = $exploded;

        if(empty($token)) {
            return null;
        }

        return $token;
    }

    public function authenticate(Request $request): Passport
    {
        $jwt = $this->getHeaderToken($request) ?? $request->cookies->get('jwt');

        if($jwt === null) {
            throw new CustomUserMessageAuthenticationException();
        }

        try {
            $payload = JWT::decode($jwt, new Key($this->parameters->get('jwt_secret'), 'HS256'));
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
            setcookie('jwt', '', time() - 1, path: '/', secure: $request->isSecure(), httponly: true);
            throw new CustomUserMessageAuthenticationException("bad_token");
        } catch(Exception) {
            throw new CustomUserMessageAuthenticationException();
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
//        return new JsonResponse([
//            'success' => false,
//            'error' => $exception->getMessageKey(),
//        ], Response::HTTP_UNAUTHORIZED);
    }
}
