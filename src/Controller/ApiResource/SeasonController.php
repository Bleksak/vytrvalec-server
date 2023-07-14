<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Charity;
use App\Entity\Season;
use App\Repository\CharityRepository;
use App\Repository\SeasonRepository;
use App\Requests\SeasonCreateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Season')]
class SeasonController extends AbstractController
{
    public function __construct(private readonly SeasonRepository $seasonRepository, private readonly SerializerInterface $serializer)
    {
    }

    #[ApiRoute(
        '/api/season/create',
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
    public function create(SeasonCreateRequest $request, CharityRepository $charityRepository): Response
    {
        $errors = $request->validate();

        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $season = new Season();
        $season->setStart($request->getStart());
        $season->setEnd($request->getEnd());

        $charity = new Charity();
        $charity->setName($request->getCharityName());
        $charity->setDescription($request->getCharityDescription());

        $charityRepository->save($charity);

        $season->setCharity($charity);

        $this->seasonRepository->save($season, true);

        return $this->json([], Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/season/list',
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
    public function seasonList(): Response {
        $seasons = $this->serializer->normalize($this->seasonRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($seasons);
    }

    #[ApiRoute(
        '/api/season/{season}/delete',
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
        if(!$season->canDelete()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->seasonRepository->remove($season);
        return new Response(status: Response::HTTP_OK);
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
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
            ]
        ],
    )]
    public function season(Season $season): Response {
        $season = $this->serializer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($season);
    }

}