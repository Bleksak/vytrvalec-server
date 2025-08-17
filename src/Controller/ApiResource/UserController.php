<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\UserActions;
use App\Dto\EmailingChangeDto;
use App\Dto\PasswordChangeDto;
use App\Dto\User\PasswordResetDto;
use App\Dto\User\PasswordResetRequestDto;
use App\Dto\User\Response\UserLoginResponseDto;
use App\Dto\User\Response\UserResponseDto;
use App\Dto\User\UserEditDto;
use App\Dto\User\UserLoginDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OA\Tag(name: 'User')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly UserRepository $userRepository,
        private readonly UserActions $action,
    ) {}

    #[OA\Post(description: 'Log the user in and generate a JWT token')]
    #[Route('/api/user/login', name: 'api_user_login', methods: ['POST'])]
    public function login(#[MapRequestPayload] UserLoginDto $dto, ParameterBagInterface $bag): Response
    {
        $user = $this->action->login($dto);

        if ($user === null) {
            return new Response(status: 404);
        }

        $expirationTime = time() + (10 * 365 * 24 * 60 * 60); // 10 years expiration

        $payload = [
            'kid' => $user->getId(),
            'user' => $user->getUserIdentifier(),
            'exp' => $expirationTime,
        ];

        /** @var string */
        $key = $bag->get('jwt_secret');

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $this->json(new UserLoginResponseDto($user->toResponseObject(), $jwt));
    }

    #[OA\Get(description: 'Clears the JWT cookie. If user is not logged in, just no-op.', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Succesfully logged out',
        ),
    ])]
    #[Route('/api/user/logout', name: 'api_user_logout', methods: ['GET'], env: 'dev')]
    public function logout(): Response
    {
        $response = new Response(status: Response::HTTP_OK);
        $response->headers->clearCookie('jwt');

        return $response;
    }

    #[Route('/api/user/count', name: 'api_user_count', methods: ['GET']
        // documentation: 'Retrieve count of <code>User</code> entities',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Retrieved count of User entities',
        //         'response' => ['integer']
        //     ],
        // ]
    )]
    public function userCount(): Response
    {
        return $this->json($this->userRepository->count(['banned' => false]));
    }

    #[Route('/api/user/current', name: 'api_user_current_profile', methods: ['GET']
        // documentation: 'Retrieve a <code>User</code> entity',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieves a User entity',
        //         'response' => [
        //             'id' => 'integer',
        //             'email' => 'string',
        //             'roles' => ['ROLE_USER', 'ROLE_STAFF'],
        //             'faculty' => [
        //                 'id' => 'integer',
        //                 'name' => 'string',
        //                 'shortcut' => 'string',
        //             ]
        //         ]
        //     ],
        //     Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        // ],
    )]
    public function currentUserData(#[CurrentUser] User $currentUser): Response
    {
        return $this->json($currentUser->toResponseObject());
    }

    #[OA\Get(
        description: 'Request a password change',
        parameters: [
            new OA\Parameter(
                name: 'lang',
                in: 'path',
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'Contains the e-mail address of the request',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PasswordResetDto::class)),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Succesfully updated',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Invalid data',
            ),
        ],
    )]
    #[Route('/api/user/password/{lang}', name: 'api_user_forgotten_password_request', methods: ['POST'])]
    public function forgottenPasswordRequest(#[MapRequestPayload] PasswordResetRequestDto $dto): Response
    {
        $this->action->forgottenPasswordRequest($dto->email);

        return new Response(status: Response::HTTP_OK);
    }

    #[Route('/api/user/reset-password', name: 'api_user_forgotten_password', methods: ['POST']
        // documentation: 'Resets the users\' password if the token is valid',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully changed password',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Bad request',
        //     ]
        // ]
    )]
    public function forgottenPasswordReset(#[MapRequestPayload] PasswordResetDto $dto): Response
    {
        $errors = $this->action->forgottenPasswordReset($dto);

        if (count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_OK);
    }

    #[Route('/api/user/{user}', name: 'api_user_profile', methods: ['GET']
        // documentation: 'Retrieve a <code>User</code> entity',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieves a User entity',
        //         'response' => [
        //             'id' => 'integer',
        //             'email' => 'string',
        //             'roles' => ['ROLE_USER', 'ROLE_STAFF'],
        //             'faculty' => [
        //                 'id' => 'integer',
        //                 'name' => 'string',
        //                 'shortcut' => 'string',
        //             ]
        //         ]
        //     ],
        //     Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        // ],
    )]
    public function userData(#[CurrentUser] User $currentUser, User $user): Response
    {
        if (!$this->isGranted('ROLE_STAFF')) {
            $user = $currentUser;
        }

        $filtered = $this->normalizer->normalize($user, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'userSummaries'],
        ]);

        return $this->json($filtered);
    }

    #[Route('/api/user', name: 'api_user_list', methods: ['GET']
        // documentation: 'Retrieve all <code>User</code> entities',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieved all User entities',
        //         'response' => [
        //             [
        //                 'id' => 'integer',
        //                 'email' => 'string',
        //                 'roles' => ['ROLE_USER', 'ROLE_STAFF'],
        //                 'faculty' => [
        //                     'id' => 'integer',
        //                     'name' => 'string',
        //                     'shortcut' => 'string',
        //                 ]
        //             ]
        //         ]
        //     ],
        //     Response::HTTP_UNAUTHORIZED => ['message' => 'Unauthorized access'],
        // ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function userList(): Response
    {
        return $this->json(array_map(
            static fn(User $user): UserResponseDto => $user->toResponseObject(),
            $this->userRepository->findAllNotDeleted(),
        ));
    }

    #[Route('/api/user', name: 'api_user_register', methods: ['POST']
        // documentation: 'Creates a new <code>User</code> entity',
        // responses: [
        //     Response::HTTP_CREATED => [
        //         'message' => 'User successfully created',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Bad request',
        //     ],
        // ],
        // requestScheme: [
        //     'email' => 'string',
        //     'password' => 'string',
        //     'first_name' => 'string',
        //     'last_name' => 'string',
        //     'faculty' => 'integer'
        // ],
    )]
    public function register(#[MapRequestPayload] UserDto $dto): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->json(['auth' => ['logged_in']], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->create($dto);

        if (count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_CREATED);
    }

    #[OA\Patch(description: 'Update a user\'s password', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Succesfully updated',
        ),
        new OA\Response(
            response: Response::HTTP_BAD_REQUEST,
            description: 'Invalid data',
        ),
    ])]
    #[Route('/api/user/change', name: 'api_user_update_password', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function updatePassword(
        #[MapRequestPayload] PasswordChangeDto $dto,
        #[CurrentUser] User $currentUser,
    ): Response {
        $errors = $this->action->updatePassword($currentUser, $dto);

        if (count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_OK);
    }

    #[Route('/api/user/anonymize', methods: ['POST'])]
    public function setAccountAnonymization(#[CurrentUser] User $user, Request $request): Response
    {
        $anonymizeValue = boolval($request->getPayload()->get('anonymize', false));
        $this->action->updateAnonymization($user, $anonymizeValue);

        return new Response();
    }

    #[OA\Get(
        description: 'Unsubscribes user from e-mail delivery.',
        parameters: [
            new OA\Parameter(
                name: 'unsubscribe_hash',
                in: 'path',
                schema: new OA\Schema(
                    type: 'string',
                    example: '4a7c7a836adcaf51eea0515028d3a3d45fb6bde835a0d25c442f1e9b27d25e6479985a8d6b6d5c20a7350b54e3e1cbac93a1',
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Successfully unsubscribed from all future e-mails',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'User with given hash not found',
            ),
        ],
    )]
    #[Route('/api/unsubscribe/{unsubscribeHash}', name: 'api_email_unsubscribe', methods: ['GET'])]
    public function unsubscribe(string $unsubscribeHash): Response
    {
        if (!$this->action->disableMailing($unsubscribeHash)) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return new Response();
    }

    #[OA\Patch(description: 'Toggles user\'s e-mail delivery.', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Successfully toggled e-mailing',
        ),
    ])]
    #[Route('/api/emailing', name: 'api_email_set_user', methods: ['PATCH'])]
    public function setMailing(
        #[CurrentUser] User $user,
        #[MapRequestPayload] EmailingChangeDto $emailingChangeDto,
    ): Response {
        $this->action->toggleMailing($user, $emailingChangeDto);

        return new Response();
    }

    #[Route('/api/user/{user}', name: 'api_user_patch', methods: ['PATCH']
        // documentation: 'Updates a <code>User</code> entity',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'User successfully edited',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Bad request',
        //     ],
        //     Response::HTTP_FORBIDDEN => [
        //         'message' => 'Forbidden access',
        //     ],
        // ],
        // requestScheme: [
        //     'email?' => 'string',
        //     'password?' => 'string',
        //     'first_name?' => 'string',
        //     'last_name?' => 'string',
        //     'faculty?' => 'integer'
        // ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function update(#[MapRequestPayload] UserEditDto $dto, User $user): Response
    {
        $this->action->update($user, $dto);

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Delete(description: 'Deletes (anonymizes) the current user')]
    #[Route('/api/user', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] User $user): Response
    {
        $this->action->delete($user);

        return new Response();
    }
}
