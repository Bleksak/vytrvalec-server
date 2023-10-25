<?php

namespace App\Controller\ApiResource;

use App\Action\SeasonActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Charity;
use App\Entity\Season;
use App\Repository\CharityRepository;
use App\Repository\FacultyCacheRepository;
use App\Repository\FacultyExtraPointsRepository;
use App\Repository\SeasonRepository;
use App\Requests\SeasonRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Season')]
class SeasonController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly SerializerInterface $serializer,
        private readonly SeasonActions $action,
    )
    {
    }

    #[ApiRoute(
        '/api/season',
        name: 'api_season_create',
        methods: ['POST'],
        documentation: 'Creates a new <code>Season</code> entity',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Successfully created a new Season entity',
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'start' => 'invalid_date',
                    'end' => 'before_start',
                ]
            ]
        ],
        requestScheme: [
            'start' => 'date',
            'end' => 'date',
            'charityName' => 'string',
            'charityDescription' => 'string'
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(SeasonRequest $request, CharityRepository $charityRepository): Response
    {
        $errors = $request->validate();

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $season = new Season($request->getStart(), $request->getEnd(), new Charity($request->getCharityName(), $request->getCharityDescription()));

        $this->action->create($season);

        return new Response(status: Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/season/current',
        name: 'api_season_current',
        methods: ['GET'],
        documentation: 'Get the currently running <code>Season</code>',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved the currently running season',
                'response' => [
                    'id' => 'integer',
                    'start' => 'date',
                    'end' => 'date',
                    'charity' => [
                        'name' => 'string',
                        'description' => 'string'
                    ],
                ]
            ],
            Response::HTTP_NOT_FOUND => ['message' => 'Current season has not been found']
        ],
    )]
    public function current(): Response
    {
        $season = $this->seasonRepository->getCurrent();
        if ($season === false) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions']
        ]));
    }

    #[ApiRoute(
        '/api/season/{season}',
        name: 'api_season_delete',
        methods: ['DELETE'],
        documentation: 'Retrieves a <code>Season</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully deleted a season entity',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ]
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Season $season): Response
    {
        if (!$season->canDelete()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->seasonRepository->remove($season);
        
        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/season/{season}/results',
        name: 'api_season_results',
        methods: ['GET'],
        documentation: 'Retrieves a <code>Season</code>\'s results',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully calculated results',
                'response' => [
                    [
                        'weekId' => [
                            'activityId' => [
                                'facultyId' => [
                                    'distance' => 'int',
                                    'elevation' => 'int',
                                ],
                                'extras' => [
                                    'weekId' => [
                                        'name' => 'weekly_distance|daily_distance|weekly_elevation',
                                        'user_id' => 'int',
                                        'distance|elevation' => 'int',
                                        'faculty' => 'int',
                                        'reward' => 'int',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad request']
        ],
    )]
    public function result(Season $season, FacultyCacheRepository $facultyCacheRepository, FacultyExtraPointsRepository $extraPointsRepository): Response
    {
        return $this->json($this->serializer->normalize($facultyCacheRepository->findCaches($season), null, [
            AbstractNormalizer::GROUPS => ['fetchSeasonResult']
        ]));
    }

    #[ApiRoute(
        '/api/season/{season}',
        name: 'api_season',
        methods: ['GET'],
        documentation: 'Retrieves a <code>Season</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved a season entity',
                'response' => [
                    'id' => 'integer',
                    'start' => 'date',
                    'end' => 'date',
                    'charity' => [
                        'name' => 'string',
                        'description' => 'string'
                    ],
                ]
            ],
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad request']
        ],
    )]
    public function season(Season $season): Response
    {
        $season = $this->serializer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($season);
    }

    #[ApiRoute(
        '/api/season',
        name: 'api_season_list',
        methods: ['GET'],
        documentation: 'Get all seasons',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all seasons',
                'response' => [
                    [
                        'id' => 'integer',
                        'start' => 'date',
                        'end' => 'date',
                        'charity' => [
                            'name' => 'string',
                            'description' => 'string'
                        ],
                    ]
                ]
            ]
        ],
    )]
    public function seasonList(): Response
    {
        $seasons = $this->serializer->normalize($this->seasonRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['submissions'],
        ]);

        return $this->json($seasons);
    }
}
