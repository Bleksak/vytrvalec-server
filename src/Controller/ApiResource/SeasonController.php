<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Charity;
use App\Entity\Season;
use App\Repository\CharityRepository;
use App\Repository\SeasonRepository;
use App\Requests\SeasonCreateRequest;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Season')]
class SeasonController extends AbstractController
{
    public function __construct(private readonly SeasonRepository $seasonRepository, private readonly SerializerInterface $serializer)
    {
    }

    #[Route('/api/season/createTest', name: 'api_season_create_test', methods: ['GET'], env: 'dev')]
    public function createTest(EntityManagerInterface $em, CharityRepository $charityRepository): Response
    {
        $season = new Season();
        $now = new DateTimeImmutable();
        $season->setStart($now);
        $end = $now->add(new DateInterval('P4W'));
        $season->setEnd($end);

        $charity = new Charity();
        $charity->setName('Sbirka na nohu Kasparovy');
        $charity->setDescription('Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.');

        $charityRepository->save($charity);

        $season->setCharity($charity);

        $this->seasonRepository->save($season, true);

        return $this->json([
            'success' => true
        ]);
    }

    #[ApiRoute(
        '/api/season/create',
        name: 'api_season_create',
        methods: ['POST'],
        documentation: '',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Successfully created a new Season entity',
                'response' => [
                    'success' => true
                ]
            ],
            Response::HTTP_UNAUTHORIZED => [
                'message' => 'Unauthorized access',
                'response' => [
                    'success' => false,
                ]
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request',
                'response' => [
                    'success' => false,
                    'errors' => [
                        'beginDate' => 'invalid_date',
                        'endDate' => 'before_beginDate',
                    ]
                ]
            ]
        ],
        requestScheme: [
            'beginDate' => 'date',
            'endDate' => 'date',
            'charityName' => 'string',
            'charityDescription' => 'string'
        ],
    )]
    public function create(SeasonCreateRequest $request, EntityManagerInterface $em, CharityRepository $charityRepository): Response
    {
        // TODO: change to ROLE_STAFF
        if(!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'success' => false
            ], Response::HTTP_UNAUTHORIZED);
        }

        $errors = $request->validate();

        if(!empty($errors)) {
            dd($errors);
        }

        $season = new Season();
        $season->setStart($request->getBeginDate());
        $season->setEnd($request->getEndDate());

        $charity = new Charity();
        $charity->setName($request->getCharityName());
        $charity->setDescription($request->getCharityDescription());

        $charityRepository->save($charity);

        $season->setCharity($charity);

        $this->seasonRepository->save($season, true);

        return $this->json([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/season/list', name: 'api_season_list', methods: ['GET'])]
    public function seasonList(): Response {
        $seasons = $this->serializer->normalize($this->seasonRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($seasons);
    }

    #[Route('/api/season/get/{season}', name: 'api_season', methods: ['GET'])]
    public function season(Season $season): Response {

        $season = $this->serializer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($season);
    }
}