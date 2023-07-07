<?php

namespace App\Security;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class LoginAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly UserProviderInterface $userProvider, private readonly UserPasswordHasherInterface $hasher, private readonly SerializerInterface $serializer)
    {
    }
    public function supports(Request $request): ?bool
    {
        return $request->get('_route') === 'api_user_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
//        $request->getPayload();
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if($email === null || $password === null) {
            throw new CustomUserMessageAuthenticationException('Unauthorized access');
        }

        return new SelfValidatingPassport(
            new UserBadge($email, function ($email) use ($password) {
                $user = $this->userProvider->loadUserByIdentifier($email);

                if(!$this->hasher->isPasswordValid($user, $password)) {
                    throw new UserNotFoundException();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $expirationTime = time() + 10 * 365 * 24 * 60 * 60; // 10 years expiration

        $payload = [
            'kid' => $token->getUser()->getId(),
            'user' => $token->getUserIdentifier(),
            'exp' => $expirationTime,
        ];

        $key = $request->server->get('JWT_SECRET');

        $jwt = JWT::encode($payload, $key, 'HS256');

        $response = new JsonResponse([
            'success' => true,
            'token' => $jwt,
            'user' => $this->serializer->normalize($token->getUser(), null, [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries']]),
        ]);

        $response->headers->setCookie(new Cookie('jwt', $jwt, $expirationTime, secure: $request->isSecure(), httpOnly: true));

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'success' => false,
            'error' => $exception->getMessageKey(),
        ], Response::HTTP_BAD_REQUEST);
    }
}
