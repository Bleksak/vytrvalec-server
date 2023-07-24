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
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ApiResource(resourceName: 'User')]
class UserApiController extends AbstractController
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly UserRepository $userRepository)
    {
    }

    #[ApiRoute(
        '/api/unused/login',
        methods: ['POST'],
        env: 'dev',
        documentation: 'Creates a JWT cookie, returns a JWT token',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Token successfully created',
                'response' => [
                    'token' => 'string',
                    'user' => [
                        'id' => 'integer',
                        'email' => 'string',
                        'roles' => ['ROLE_USER', 'ROLE_STAFF'],
                        'faculty' => [
                            'id' => 'integer',
                            'name' => 'string',
                            'shortcut' => 'string',
                        ]
                    ]
                ]
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Bad request',
            ],
        ],
        requestScheme: [
            'email' => 'string',
            'password' => 'string',
        ],
        fakeName: 'api_user_login',
        fakePath: '/api/user/login',
    )]
    public function login(): Response
    {
        return $this->json([]);
    }

    #[ApiRoute(
        '/api/unused/logout',
        methods: ['GET'],
        env: 'dev',
        documentation: 'Clears the JWT cookie, this endpoint has no effect if using HTTP authentication',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Cookie successfully cleared',
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Unauthorized access',
            ],
        ],
        fakeName: 'api_user_logout',
        fakePath: '/api/user/logout',
    )]
    public function logout(): Response
    {
        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/user/register',
        name: 'api_user_register',
        methods: ['POST'],
        documentation: 'Creates a new <code>User</code> entity',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'User successfully created',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
            ],
        ],
        requestScheme: [
            'email' => 'string',
            'password' => 'string',
            'firstName' => 'string',
            'lastName' => 'string',
            'faculty' => 'integer'
        ],
    )]
    public function register(RegistrationRequest $request, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $hasher): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->json([
                // TODO: message
                'TODO: nelze registrovat protoze uz je prihlasenej'
            ], Response::HTTP_BAD_REQUEST);
        }

        $messages = $request->validate();

        if (count($messages) != 0) {
            return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setEmail($request->getEmail());
        $user->setPassword($hasher->hashPassword($user, $request->getPassword()));
        $user->setFaculty($request->getFaculty());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());

        $constraints = $validator->validate($user);
        if (count($constraints) != 0) {
            $messages = [];
            foreach ($constraints as $constraint) {
                $messages[] = $constraint->getMessage();
            }

            return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/user/count',
        name: 'api_user_count',
        methods: ['GET'],
        documentation: 'Retrieve count of <code>User</code> entities',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Retrieved count of User entities',
                'response' => ['integer']
            ],
        ]
    )]
    public function userCount(): Response
    {
        return $this->json($this->userRepository->count(['banned'=>false]));
    }

    #[ApiRoute(
        '/api/user/list',
        name: 'api_user_list',
        methods: ['GET'],
        documentation: 'Retrieve all <code>User</code> entities',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all User entities',
                'response' => [
                    [
                        'id' => 'integer',
                        'email' => 'string',
                        'roles' => ['ROLE_USER', 'ROLE_STAFF'],
                        'faculty' => [
                            'id' => 'integer',
                            'name' => 'string',
                            'shortcut' => 'string',
                        ]
                    ]
                ]
            ],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function userList(): Response
    {
        return $this->json($this->serializer->normalize($this->userRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]));
    }

    #[ApiRoute(
        '/api/user/profile',
        name: 'api_user_current_profile',
        methods: ['GET'],
        documentation: 'Retrieve a currently logged <code>User</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieves a User entity',
                'response' => [
                    'id' => 'integer',
                    'email' => 'string',
                    'roles' => ['ROLE_USER', 'ROLE_STAFF'],
                    'faculty' => [
                        'id' => 'integer',
                        'name' => 'string',
                        'shortcut' => 'string',
                    ]
                ]
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
        ],
    )]
    #[IsGranted('ROLE_USER')]
    public function currentUserData(#[CurrentUser] User $user): Response
    {
        return $this->json($this->serializer->normalize($user, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]));
    }

    #[ApiRoute(
        '/api/user/{user}/profile',
        name: 'api_user_profile',
        methods: ['GET'],
        documentation: 'Retrieve a <code>User</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieves a User entity',
                'response' => [
                    'id' => 'integer',
                    'email' => 'string',
                    'roles' => ['ROLE_USER', 'ROLE_STAFF'],
                    'faculty' => [
                        'id' => 'integer',
                        'name' => 'string',
                        'shortcut' => 'string',
                    ]
                ]
            ],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    public function userData(#[CurrentUser] User $currentUser, User $user = null): Response
    {
        if (!$this->isGranted('ROLE_STAFF')) {
            $user = $currentUser;
        }

        $filtered = $this->serializer->normalize($user, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]);

        return $this->json($filtered);
    }
}