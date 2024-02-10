<?php

namespace App\Controller\ApiResource;

use App\Action\SeasonActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\CustomLogic\SeasonResult;
use App\Entity\Season;
use App\Form\SeasonFormType;
use App\Repository\CacheRepository;
use App\Repository\SeasonRepository;
use App\Validation\FormErrors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ApiResource('Season')]
class SeasonController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly SeasonActions $action,
    ) {
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
    public function create(Request $request): Response
    {
        $form = $this->createForm(SeasonFormType::class);

        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);
        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $id = $this->action->create($form->getData());

        if($id === -1) {
            return $this->json(['season' => 'season_exists'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['id' => $id], Response::HTTP_CREATED);
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
        if ($season === null) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->normalizer->normalize($season, null, [
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

        $this->seasonRepository->remove($season, true);

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
    public function result(Season $season, SeasonResult $result, CacheRepository $cacheRepository): Response
    {
        $cache = $cacheRepository->findOneBy(['season' => $season->getId()]);

        if($cache !== null) {
            return $this->json($cache->getData());
        }

        return $this->json($result->calculate($season));
    }

    #[ApiRoute(
        '/api/season/{season}/submissions',
        name: 'api_season_submissions',
        methods: ['GET'],
        documentation: 'Retrieves all submissions from a given <code>Season</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved a season entity',
                'response' => []
            ],
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad request']
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function submissions(Season $season, Request $request): Response
    {
        $scheme = $request->getScheme();
        $hostname = $request->getHost();

        $url = $scheme . '://' . $hostname;

        return $this->json($this->normalizer->normalize($season->getSubmissions(), null, [
            AbstractNormalizer::GROUPS => ['fetchSubmission'],
            AbstractNormalizer::CALLBACKS => ['image' => fn (string $image) => $url . $image]
        ]));
    }

    #[ApiRoute(
        '/api/season/past',
        name: 'api_season_index_past',
        methods: ['GET'],
        documentation: 'Retrieves all past <code>Season</code> entities',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved entities',
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
    public function indexPast(): Response
    {
        $seasons = $this->normalizer->normalize($this->seasonRepository->findPast(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($seasons);
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
                    'charity' => 'integer',
                ]
            ],
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad request']
        ],
    )]
    public function season(Season $season): Response
    {
        $season = $this->normalizer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
            AbstractNormalizer::CALLBACKS => ['charity' => fn($charity) => $charity->getId()]
        ]);

        return $this->json($season);
    }

    #[ApiRoute(
        '/api/season',
        name: 'api_season_index',
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
                        'charity' => 'number',
                    ]
                ]
            ]
        ],
    )]
    public function seasonList(): Response
    {
        $seasons = $this->normalizer->normalize($this->seasonRepository->findOrdered(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['submissions'],
            AbstractNormalizer::CALLBACKS => ['charity' => fn($charity) => $charity->getId()]
        ]);

        return $this->json($seasons);
    }
}
