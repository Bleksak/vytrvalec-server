<?php

namespace App\Controller\ApiResource;

use App\Action\UserActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\User;
use App\Form\UserCreateFormType;
use App\Repository\UserRepository;
use App\Validation\FormErrors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ApiResource(resourceName: 'User')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly UserRepository $userRepository,
        private readonly UserActions $action,
    ) {
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
            'firebase_token' => 'string',
        ],
        fakeName: 'api_user_login',
        fakePath: '/api/user/login',
    )]
    public function login(): Response
    {
        return new Response(status: Response::HTTP_NOT_FOUND);
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
        return new Response(status: Response::HTTP_NOT_FOUND);
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
        return $this->json($this->userRepository->count(['banned' => false]));
    }

    #[ApiRoute(
        '/api/user/current',
        name: 'api_user_current_profile',
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
    public function currentUserData(#[CurrentUser] User $currentUser): Response
    {
        $filtered = $this->normalizer->normalize($currentUser, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]);

        return $this->json($filtered);
    }

    #[ApiRoute(
        '/api/user/{user}',
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

        $filtered = $this->normalizer->normalize($user, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]);

        return $this->json($filtered);
    }

    #[ApiRoute(
        '/api/user',
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
        return $this->json($this->normalizer->normalize($this->userRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]));
    }

    #[ApiRoute(
        '/api/user',
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
            'first_name' => 'string',
            'last_name' => 'string',
            'faculty' => 'integer'
        ],
    )]
    public function register(Request $request): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->json(['auth' => ['logged_in']], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(UserCreateFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->create($form->getData());

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_CREATED);
    }
}
