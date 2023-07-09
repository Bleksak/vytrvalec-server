<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
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

#[ApiResource(resourceName: 'User')]
class UserApiController extends AbstractController {
    public function __construct(private readonly SerializerInterface $serializer, private readonly UserRepository $userRepository)
    {
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


    #[ApiRoute(
        methods: ['POST'],
        documentation: 'Creates a JWT cookie, returns a JWT token',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Cookie successfully created',
                'response' => [
                    'success' => true,
                ]
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Bad request',
                'response' => [
                    'success' => false,
                    'errors' => [
                        'bad_request'
                    ]
                ]
            ],
        ],
        requestScheme: [
            'email' => 'string',
            'password' => 'string',
        ],
        fakeName: 'api_user_login',
        fakePath: '/api/user/login',
    )]
    public function login(): Response {
        return $this->json([]);
    }

    #[ApiRoute(
        '/api/user/register',
        name: 'api_user_register',
        methods: ['POST'],
        documentation: 'Creates a new User entity',
        responses: [
            201 => [
                'message' => 'User successfully created',
                'response' => [
                    'success' => true,
                ]
            ],
            400 => [
                'message' => 'Bad request',
                'response' => [
                    'success' => false,
                    'errors' => [
                        'bad_request'
                    ]
                ]
            ],
        ],
        requestScheme: [
            'email' => 'string',
            'password' => 'string',
            'firstName' => 'string',
            'lastName' => 'string',
            'faculty' => 'int'
        ],
    )]
    public function register(RegistrationRequest $request, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $hasher, UserInterface $userInterface = null): Response
    {
        $messages = $request->validate();

        if(count($messages) != 0) {
            return $this->json([
                'success' => false,
                'errors' => $messages
            ], Response::HTTP_BAD_REQUEST);
        }

        if($userInterface !== null) {
            return $this->json([
                'success' => false,
                // TODO: message
                'errors' => ['TODO: nelze registrovat protoze uz je prihlasenej']
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setEmail($request->getEmail());
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

            return $this->json([
                'success' => false,
                'errors' => $messages
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'success' => true
        ], Response::HTTP_CREATED);
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