<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\SeasonActions;
use App\CustomLogic\SeasonResultCalculator;
use App\Dto\Season\Response\SeasonIndexResponseDto;
use App\Dto\Season\SeasonCreateDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Activity;
use App\Entity\Charity;
use App\Entity\Faculty;
use App\Entity\Image;
use App\Entity\Season;
use App\Entity\Submission;
use App\Repository\SeasonCacheRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Schema\SeasonWithoutSubmissionsSchema;
use App\Services\ImagePath;
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

#[OA\Tag(name: 'Season')]
final class SeasonController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly SeasonActions $action,
        private readonly ImagePath $imagePath,
    ) {
    }

    #[OA\Post(
        description: 'Create a new Season',
        requestBody: new OA\RequestBody(
            description: 'The new Season',
            required: true,
            content: new OA\JsonContent(ref: new Model(type: SeasonCreateDto::class)),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Season created',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ],
    )]
    #[Route('/api/season', name: 'api_season_create', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function create(#[MapRequestPayload] SeasonCreateDto $dto): Response
    {
        $id = $this->action->create($dto);

        if ($id === -1) {
            return $this->json(['season' => 'season_exists'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['id' => $id], Response::HTTP_CREATED);
    }

    #[OA\Get(description: 'Retrieve currently running season', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'The running season',
            content: new OA\JsonContent(ref: new Model(type: SeasonWithoutSubmissionsSchema::class)),
        ),
        new OA\Response(
            response: Response::HTTP_NOT_FOUND,
            description: 'Season is currently not running',
        ),
    ])]
    #[Route('/api/season/current', name: 'api_season_current', methods: ['GET'])]
    public function current(): Response
    {
        $season = $this->seasonRepository->getCurrent();
        if ($season === null) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->normalizer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]));
    }

    #[OA\Delete(
        description: 'Delete a non running Season. This request will fail if the Season is running or contains any submissions.',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Season successfully deleted',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Season is already running and cannot be deleted.',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access.',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Provided Season does not exist.',
            ),
        ],
    )]
    #[Route('/api/season/{season}', name: 'api_season_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Season $season): Response
    {
        if (!$season->canDelete()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->seasonRepository->remove($season, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Get(
        description: 'Retrieve Season results',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Season results',
                content: new OA\JsonContent(ref: new Model(type: WeeklyResultDto::class)),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Season does not exist',
            ),
        ],
    )]
    #[Route('/api/season/{season}/results', name: 'api_season_results', methods: ['GET'])]
    public function result(
        Season $season,
        SeasonResultCalculator $result,
        SeasonCacheRepository $cacheRepository,
    ): Response {
        $cache = $cacheRepository->findOneBy(['season' => $season->getId()]);

        if ($cache !== null) {
            return $this->json($cache->getData());
        }

        return $this->json($result->calculate($season));
    }

    #[OA\Get(
        description: 'Retrieve all submissions from a given Season entity and query filter',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Season successfully deleted',
                content: new OA\JsonContent(ref: new Model(type: Submission::class)),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Provided Season does not exist.',
            ),
        ],
    )]
    #[Route('/api/season/{season}/submissions', name: 'api_season_submissions', methods: ['GET'])]
    #[IsGranted('ROLE_STAFF')]
    public function submissions(SubmissionRepository $submissionRepository, Season $season, Request $request): Response
    {
        $queryFilterKeys = [
            'date',
            'week',
            'accepted',
            'reviewed',
            'user',
            'faculty',
            'activity',
        ];

        /**
         * @var array<string, string|int> $queryFilter
         */
        $queryFilter = [];
        foreach ($queryFilterKeys as $key) {
            $data = $request->get($key, null);

            if ($data !== null && (is_string($data) || is_int($data))) {
                $queryFilter[$key] = $data;
            }
        }

        $currentPage = $request->get('page', 1);

        if (!is_int($currentPage)) {
            $currentPage = 1;
        }

        $results = $submissionRepository->findBySeasonAndFilter($season, $queryFilter, $currentPage, 25);

        return $this->json(
            $results,
            200,
            [],
            [
                AbstractNormalizer::GROUPS => ['fetchSubmission'],
                AbstractNormalizer::CALLBACKS => [
                    'image' => fn (Image $image): string => $this->imagePath->fullPath($image),
                    'activity' => static fn (Activity $activity): int => $activity->getId(),
                    'faculty' => static fn (Faculty $faculty): int => $faculty->id,
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['season'],
            ],
        );
    }

    #[OA\Get(description: 'Retrieve all past seasons', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'The running season',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: new Model(type: SeasonWithoutSubmissionsSchema::class)),
            ),
        ),
    ])]
    #[Route('/api/season/past', name: 'api_season_index_past', methods: ['GET'])]
    public function indexPast(): Response
    {
        $seasons = $this->normalizer->normalize($this->seasonRepository->findPast(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
        ]);

        return $this->json($seasons);
    }

    #[OA\Get(
        description: 'Retrieve a Season by ID',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Season',
                content: new OA\JsonContent(ref: new Model(type: SeasonWithoutSubmissionsSchema::class)),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Provided Season does not exist.',
            ),
        ],
    )]
    #[Route('/api/season/{season}', name: 'api_season', methods: ['GET'])]
    public function season(Season $season): Response
    {
        $season = $this->normalizer->normalize($season, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['facultySummaries', 'userSummaries', 'submissions'],
            AbstractNormalizer::CALLBACKS => ['charity' => fn (Charity $charity): int => $charity->getId()],
        ]);

        return $this->json($season);
    }

    #[OA\Get(description: 'Retrieve all Seasons', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'All Seasons',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: new Model(type: SeasonIndexResponseDto::class)),
            ),
        ),
    ])]
    #[Route('/api/season', name: 'api_season_index', methods: ['GET'])]
    public function index(ImagePath $imagePath): Response
    {
        return $this->json(array_map(
            fn (Season $season): SeasonIndexResponseDto => $season->toResponseObject($imagePath),
            $this->seasonRepository->findOrdered(),
        ));
    }
}
