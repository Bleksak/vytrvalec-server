<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\UserActions;
use App\Dto\EmailingChangeDto;
use App\Dto\PasswordChangeDto;
use App\Dto\PasswordChangeRequestDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Form\PasswordResetFormType;
use App\Form\UserEditFormType;
use App\Repository\UserRepository;
use App\Validation\FormErrors;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ) {
    }

    #[Route(
        '/api/unused/login',
        methods: ['POST'],
        env: 'dev',
        // documentation: 'Creates a JWT cookie, returns a JWT token',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Token successfully created',
        //         'response' => [
        //             'token' => 'string',
        //             'user' => [
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
        //     Response::HTTP_UNAUTHORIZED => [
        //         'message' => 'Bad request',
        //     ],
        // ],
        // requestScheme: [
        //     'email' => 'string',
        //     'password' => 'string',
        //     'firebase_token' => 'string',
        // ],
        // fakeName: 'api_user_login',
        // fakePath: '/api/user/login',
    )]
    public function login(): Response
    {
        return new Response(status: Response::HTTP_NOT_FOUND);
    }

    #[Route(
        '/api/unused/logout',
        methods: ['GET'],
        env: 'dev',
        // documentation: 'Clears the JWT cookie, this endpoint has no effect if using HTTP authentication',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Cookie successfully cleared',
        //     ],
        //     Response::HTTP_UNAUTHORIZED => [
        //         'message' => 'Unauthorized access',
        //     ],
        // ],
        // fakeName: 'api_user_logout',
        // fakePath: '/api/user/logout',
    )]
    public function logout(): Response
    {
        return new Response(status: Response::HTTP_NOT_FOUND);
    }

    #[Route(
        '/api/user/count',
        name: 'api_user_count',
        methods: ['GET'],
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

    #[Route(
        '/api/user/current',
        name: 'api_user_current_profile',
        methods: ['GET'],
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
        $filtered = $this->normalizer->normalize($currentUser, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'submissions', 'user'],
        ]);

        return $this->json($filtered);
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
            content: new OA\JsonContent(
                ref: new Model(type: PasswordChangeRequestDto::class),
            ),
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
        ]
    )]
    #[Route(
        '/api/user/password/{lang}',
        name: 'api_user_forgotten_password_request',
        methods: ['POST'],
    )]
    public function forgottenPasswordRequest(
        #[MapRequestPayload]
        PasswordChangeRequestDto $passwordChangeRequestDto,
        string $lang = 'cs',
    ): Response {
        $supportedLanguages = ['cs', 'en'];
        if (!in_array($lang, $supportedLanguages, true)) {
            $lang = 'cs';
        }

        $this->action->forgottenPasswordRequest($passwordChangeRequestDto->email, $lang);

        return new Response(status: Response::HTTP_OK);
    }

    #[Route(
        '/api/user/reset-password',
        name: 'api_user_forgotten_password',
        methods: ['POST'],
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
    public function forgottenPasswordReset(Request $request): Response
    {
        $form = $this->createForm(PasswordResetFormType::class);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            return $this->json(FormErrors::collect($form), Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->forgottenPasswordReset($form->getData());

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_OK);
    }

    #[Route(
        '/api/user/{user}',
        name: 'api_user_profile',
        methods: ['GET'],
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

    #[Route(
        '/api/user',
        name: 'api_user_list',
        methods: ['GET'],
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
        return $this->json($this->normalizer->normalize($this->userRepository->findAllNotDeleted(), null, [
            AbstractNormalizer::GROUPS => ['fetchUser'],
        ]));
    }

    #[Route(
        '/api/user',
        name: 'api_user_register',
        methods: ['POST'],
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

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_CREATED);
    }

    #[OA\Patch(
        description: 'Update a user\'s password',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Succesfully updated',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Invalid data',
            ),
        ]
    )]
    #[Route(
        '/api/user/change',
        name: 'api_user_update_password',
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_USER')]
    public function updatePassword(
        #[MapRequestPayload]
        PasswordChangeDto $dto,
        #[CurrentUser]
        User $currentUser,
    ): Response {
        $errors = $this->action->updatePassword($currentUser, $dto);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_OK);
    }

    #[Route(
        '/api/user/gdpr',
        methods: ['POST'],
    )]
    public function setAccountGdpr(
        #[CurrentUser]
        User $user,
        Request $request,
    ): Response {
        $gdprValue = $request->getPayload()->get('gdpr', false);
        $this->action->updateGdpr($user, $gdprValue);

        return new Response();
    }

    #[OA\Get(
        description: 'Unsubscribes user from e-mail delivery.',
        parameters: [
            new OA\Parameter(
                name: 'unsubscribe_hash',
                in: 'path',
                schema: new OA\Schema(type: 'string', example: '4a7c7a836adcaf51eea0515028d3a3d45fb6bde835a0d25c442f1e9b27d25e6479985a8d6b6d5c20a7350b54e3e1cbac93a1'),
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
    public function unsubscribe(
        ?string $unsubscribeHash = null,
    ): Response {
        if (!$this->action->disableMailing($unsubscribeHash)) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return new Response();
    }

    #[OA\Patch(
        description: 'Toggles user\'s e-mail delivery.',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Successfully toggled e-mailing',
            ),
        ],
    )]
    #[Route('/api/emailing', name: 'api_email_set_user', methods: ['PATCH'])]
    public function setMailing(
        #[CurrentUser]
        User $user,
        #[MapRequestPayload]
        EmailingChangeDto $emailingChangeDto,
    ): Response {
        $this->action->toggleMailing($user, $emailingChangeDto);

        return new Response();
    }

    #[Route(
        '/api/user/{user}',
        name: 'api_user_patch',
        methods: ['PATCH'],
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
    public function update(Request $request, ?User $user = null): Response
    {
        $form = $this->createForm(UserEditFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($user, $form->getData());

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Delete(
        description: 'Deletes (anonymizes) the current user',
    )]
    #[Route(
        '/api/user',
        name: 'api_user_delete',
        methods: ['DELETE']
    )]
    public function delete(
        #[CurrentUser]
        User $user,
    ): Response {
        $this->action->delete($user);

        return new Response();
    }
}
