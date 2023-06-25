<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserApiController extends AbstractController {
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route('/api/user/current', name: 'api_user_current', methods: ['GET'])]
    public function currentUser(UserInterface $userInterface = null): Response
    {
        $filtered = $this->serializer->normalize($userInterface, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['id', 'password', 'submissions'],
        ]);

        return $this->json([
            'success' => $userInterface !== null,
            'user' => $filtered,
        ]);
    }

    #[Route('/api/user/login', name: 'api_user_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if($user === null) {
            return $this->json([
                'success' => false,
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->currentUser($user);
    }
}