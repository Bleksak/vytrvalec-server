<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Requests\RegistrationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserApiController extends AbstractController {
    public function __construct(private readonly SerializerInterface $serializer, private readonly UserRepository $userRepository)
    {
    }

    #[Route('/api/user/login', name: 'api_user_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if($user === null) {
            return $this->json([
                'success' => false,
                'message' => 'missing_credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->userData($user);
    }

    #[Route('/api/user/submissions/{user}', name: 'api_user_submissions', methods: ['GET'])]
    public function userSubmissions(#[CurrentUser] User $currentUser, User $user = null): Response
    {
        if($user === null || ($user !== $currentUser && $currentUser->hasRole('ROLE_STAFF'))) {
            $user = $currentUser;
        }

        $filtered = $this->serializer->normalize($user->getSubmissions(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
        ]);

        return $this->json([
            'success' => true,
            'submissions' => $filtered,
        ]);
    }

    #[Route('/api/user/register', name: 'api_user_register', methods: ['POST'])]
    public function register(RegistrationRequest $request, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $hasher, UserInterface $userInterface = null): Response
    {
        if($userInterface !== null) {
            dd($userInterface);
            return $request->getResponse(false, [
                // TODO: message
                'TODO: nelze registrovat protoze uz je prihlasenej'
            ]);
        }

        $user = new User();

        $user->setEmail($request->getUsername());
        $user->setPassword($hasher->hashPassword($user, $request->getPassword()));
        $user->setFaculty($request->getFaculty());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());

        $constraints = $validator->validate($user);
        if(count($constraints) != 0) {
            $messages = [];
            foreach($constraints as $constraint) {
                $messages[] = $constraint->getMessage();
            }

            return $request->getResponse(false, $messages);
        }

        $em->persist($user);
        $em->flush();

        return $request->getResponse(true);
    }

    #[Route('/api/user/profile/{user}', name: 'api_user_profile', methods: ['GET'])]
    public function userData(#[CurrentUser] User $currentUser, User $user = null): Response
    {
        if($user === null || ($user !== $currentUser && $currentUser->hasRole('ROLE_STAFF'))) {
            $user = $currentUser;
        }

        $filtered = $this->serializer->normalize($user, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['id', 'password', 'submissions', 'userSummaries'],
        ]);

        return $this->json([
            'success' => true,
            'user' => $filtered,
        ]);
    }
}