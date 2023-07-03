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

    #[Route('/api/user/register', name: 'api_user_register', methods: ['POST'])]
    public function register(RegistrationRequest $request, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $hasher): Response
    {
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