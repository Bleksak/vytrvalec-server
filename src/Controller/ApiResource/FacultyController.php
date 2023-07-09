<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Faculty;
use App\Repository\FacultyRepository;
use App\Requests\FacultyCreateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[ApiResource('Faculty')]
class FacultyController extends AbstractController
{
    public function __construct(private readonly FacultyRepository $facultyRepository)
    {
    }

    #[ApiRoute(
        '/api/faculty/list',
        name: 'api_faculty_list',
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

    #[ApiRoute(
        '/api/faculty/create',
        name: 'api_faculty_create',
        methods: ['POST'],
        documentation: 'Create a new <code>Faculty</code> entity.',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Successfully created',
                'response' => [
                    'success' => true,
                ]
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Unauthorized access',
                'response' => [
                    'success' => false
                ]
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'success' => false,
                    'errors' => [
                        'name' => 'not_unique',
                        'shortcut' => 'not_unique',
                        'visible' => 'invalid_value'
                    ]
                ]
            ]
        ],
        requestScheme: [
            'name' => 'string',
            'shortcut' => 'string',
            'visible' => 'boolean'
        ]
    )]
    public function create(FacultyCreateRequest $request): Response
    {
        if(!$this->isGranted('ROLE_STAFF')) {
            return $this->json([
                'success' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $errors = $request->validate();

        if(!empty($errors)) {
            return $this->json([
                'success' => false,
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $faculty = new Faculty();
        $faculty->setName($request->getName());
        $faculty->setShortcut($request->getShortcut());
        $faculty->setVisible($request->getVisible());

        $this->facultyRepository->save($faculty, true);

        return $this->json([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/faculty/update/{faculty}',
        name: 'api_faculty_update',
        methods: ['PATCH'],
        documentation: 'Updates an existing <code>Faculty</code> entity.',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully updated',
                'response' => [
                    'success' => true,
                ]
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Unauthorized access',
                'response' => [
                    'success' => false
                ]
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'success' => false,
                    'errors' => [
                        'name' => 'not_unique',
                        'shortcut' => 'not_unique',
                        'visible' => 'invalid_value'
                    ]
                ]
            ]
        ],
        requestScheme: [
            'name' => 'string',
            'shortcut' => 'string',
            'visible' => 'boolean'
        ]
    )]
    public function update(Faculty $faculty, FacultyCreateRequest $request): Response
    {
        if(!$this->isGranted('ROLE_STAFF')) {
            return $this->json([
                'success' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $errors = $request->validate();

        if(!empty($errors)) {
            return $this->json([
                'success' => false,
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $faculty->setName($request->getName());
        $faculty->setShortcut($request->getShortcut());
        $faculty->setVisible($request->getVisible());

        $this->facultyRepository->save($faculty, true);

        return $this->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}