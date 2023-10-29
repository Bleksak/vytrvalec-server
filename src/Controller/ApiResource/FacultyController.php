<?php

namespace App\Controller\ApiResource;

use App\Action\FacultyActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Faculty;
use App\Form\FacultyFormType;
use App\Repository\FacultyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[ApiResource('Faculty')]
class FacultyController extends AbstractController
{
    public function __construct(
        private readonly FacultyActions $action,
        private readonly FacultyRepository $facultyRepository,
    )
    {
    }

    #[ApiRoute(
        '/api/faculty',
        name: 'api_faculty_create',
        methods: ['POST'],
        documentation: 'Create a new <code>Faculty</code> entity.',
        responses: [
            Response::HTTP_CREATED => ['message' => 'Successfully created',],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access',],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'name' => 'not_unique',
                    'shortcut' => 'not_unique',
                    'visible' => 'invalid_value'
                ]
            ]
        ],
        requestScheme: [
            'name' => 'string',
            'shortcut' => 'string',
            'visible' => 'boolean'
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(FacultyFormType::class);
        $form->submit($request->getPayload()->all());

        if(!$form->isValid()) {
            $errors = [];

            foreach($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->action->create($form->getData());

        return new Response(status: Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/faculty/{faculty}',
        name: 'api_faculty_update',
        methods: ['PATCH'],
        documentation: 'Updates an existing <code>Faculty</code> entity.',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully updated',
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'name' => 'not_unique',
                    'shortcut' => 'not_unique',
                    'visible' => 'invalid_value'
                ]
            ]
        ],
        requestScheme: [
            'name' => 'string',
            'shortcut' => 'string',
            'visible' => 'boolean'
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(Request $request, Faculty $faculty): Response
    {
        $form = $this->createForm(FacultyFormType::class, null, [
            'method' => $request->getMethod()
        ]);

        $form->submit($request->getPayload()->all());

        if(!$form->isValid()) {
            $errors = [];

            foreach($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($faculty, $form->getData());

        return $this->json([], Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/faculty/{faculty}',
        name: 'api_faculty_get',
        methods: ['GET'],
        documentation: 'Retrieve a faculty',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Faculty',
                'response' => [
                    [
                        'id' => 'integer',
                        'name' => 'string',
                        'shortcut' => 'string',
                        'visible' => 'boolean'
                    ]
                ]
            ]
        ],
    )]
    public function faculty(Faculty $faculty): Response
    {
        return $this->json($faculty);
    }

    #[ApiRoute(
        '/api/faculty',
        name: 'api_faculty_index',
        methods: ['GET'],
        documentation: 'Retrieves a list of all faculties',
        responses: [
            Response::HTTP_OK => [
                'message' => 'List of all faculties',
                'response' => [
                    [
                        'id' => 'integer',
                        'name' => 'string',
                        'shortcut' => 'string',
                        'visible' => 'boolean'
                    ]
                ]
            ]
        ],
    )]
    public function facultyList(): Response
    {
        return $this->json($this->facultyRepository->findAll());
    }
}
