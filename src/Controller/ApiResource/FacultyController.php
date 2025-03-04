<?php

namespace App\Controller\ApiResource;

use App\Action\FacultyActions;
use App\Dto\FacultyDto;
use App\Entity\Faculty;
use App\Form\FacultyFormType;
use App\Repository\FacultyRepository;
use App\Validation\FormErrors;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OA\Tag(name: 'Faculty')]
final class FacultyController extends AbstractController
{
    public function __construct(
        private readonly FacultyActions $action,
        private readonly FacultyRepository $facultyRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[OA\Post(
        description: 'Create a new Faculty',
        requestBody: new OA\RequestBody(
            description: 'The new faculty',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: FacultyDto::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Faculty created',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
                content: new OA\JsonContent(
                    description: 'List of invalid fields and their respective errors delimited by |',
                    example: [
                        'name' => 'not_unique',
                        'shortcut' => 'not_unique',
                        'visible' => 'invalid_value',
                    ]
                )
            ),
        ]
    )]
    #[Route(
        '/api/faculty',
        name: 'api_faculty_create',
        methods: ['POST'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(FacultyFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $id = $this->action->create($form->getData());

        return $this->json(['id' => $id], Response::HTTP_CREATED);
    }

    #[OA\Patch(
        description: 'Update an existing Faculty',
        parameters: [
            new OA\Parameter(
                name: 'faculty',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'The updated Faculty',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: FacultyDto::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Faculty updated',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
                content: new OA\JsonContent(
                    description: 'List of invalid fields and their respective errors delimited by |',
                    example: [
                        'name' => 'not_unique',
                        'shortcut' => 'not_unique',
                        'visible' => 'invalid_value',
                        'parent' => 'invalid_value',
                    ]
                )
            ),
        ],
    )]
    #[Route(
        '/api/faculty/{faculty}',
        name: 'api_faculty_update',
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(
        #[MapRequestPayload]
        FacultyDto $facultyDto,
        Faculty $faculty,
    ): Response {
        $errors = $this->action->update($faculty, $facultyDto);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return $this->json([], Response::HTTP_OK);
    }

    #[OA\Get(
        description: 'Retrieve a Faculty',
        parameters: [
            new OA\Parameter(
                name: 'faculty',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Faculty information',
                content: new OA\JsonContent(
                    ref: new Model(type: Faculty::class),
                ),
            ),
        ],
    )]
    #[Route(
        '/api/faculty/{faculty}',
        name: 'api_faculty_get',
        methods: ['GET'],
    )]
    public function faculty(Faculty $faculty): Response
    {
        return $this->json($faculty);
    }

    #[OA\Get(
        description: 'Retrieve a collection of all Faculties',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Faculty information',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: Faculty::class),
                    ),
                ),
            ),
        ],
    )]
    #[Route(
        '/api/faculty',
        name: 'api_faculty_index',
        methods: ['GET'],
    )]
    public function facultyList(): Response
    {
        $data = $this->normalizer->normalize(
            $this->facultyRepository->findAll(),
            null,
            [
                AbstractNormalizer::CALLBACKS => [
                    'parent' => fn (?Faculty $faculty) => $faculty?->getId(),
                ],
            ]
        );

        return $this->json($data);
    }
}
