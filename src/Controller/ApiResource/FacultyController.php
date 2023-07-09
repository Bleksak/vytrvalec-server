<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Repository\FacultyRepository;
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
            200 => [
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