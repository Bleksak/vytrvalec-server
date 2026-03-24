<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\SeasonActions;
use App\Dto\Season\Request\SeasonQueryFilterRequestDto;
use App\Dto\Season\Response\SeasonIndexResponseDto;
use App\Dto\Season\SeasonIndexDto;
use App\Dto\SeasonConfiguration\SeasonConfigurationCreateDto;
use App\Dto\Submission\Response\AdministrationSubmissionListResponseDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Schema\SeasonWithoutSubmissionsSchema;
use App\Services\ImagePath;
use App\Services\SeasonResultRankingService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Season')]
final class SeasonController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly SeasonActions $action,
        private readonly ImagePath $imagePath,
    ) {}

    #[OA\Post(description: 'Create a new Season', responses: [
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
    ])]
    #[Route('/api/season', name: 'api_season_create', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function create(
        #[MapRequestPayload]
        SeasonConfigurationCreateDto $dto,
        ImagePath $imagePath,
    ): Response {
        $result = $this->action->create($dto);

        if (\is_array($result)) {
            return $this->json($result, Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $result->toResponseObject($imagePath),
            Response::HTTP_CREATED,
        );
    }

    #[OA\Get(description: 'Retrieve currently running season', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'The running season',
            content: new OA\JsonContent(
                ref: new Model(type: SeasonWithoutSubmissionsSchema::class),
            ),
        ),
        new OA\Response(
            response: Response::HTTP_NOT_FOUND,
            description: 'Season is currently not running',
        ),
    ])]
    #[Route(
        '/api/season/current',
        name: 'api_season_current',
        methods: ['GET'],
    )]
    public function current(): Response
    {
        $season = $this->seasonRepository->findCurrentSeason();
        if ($season === null) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($season->toResponseObject());
    }

    #[OA\Delete(
        description: 'Delete a non running Season. This request will fail if the Season is running or contains any submissions.',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer', example: 1),
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
    #[Route(
        '/api/season/{season}',
        name: 'api_season_delete',
        methods: ['DELETE'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Season $season): Response
    {
        $this->action->delete($season);

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Get(
        description: 'Retrieve Season results',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Season results',
                content: new OA\JsonContent(
                    ref: new Model(type: WeeklyResultDto::class),
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Season does not exist',
            ),
        ],
    )]
    #[Route(
        '/api/season/{season}/results',
        name: 'api_season_results',
        methods: ['GET'],
    )]
    public function result(
        Season $season,
        SeasonResultRankingService $resultRankingService,
    ): Response {
        return $this->json($resultRankingService->getSeasonResult($season));
    }

    #[OA\Get(
        description: 'Retrieve all submissions from a given Season entity and query filter',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Season successfully deleted',
                content: new OA\JsonContent(
                    ref: new Model(type: Submission::class),
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Provided Season does not exist.',
            ),
        ],
    )]
    #[Route(
        '/api/season/{season}/submissions',
        name: 'api_season_submissions',
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function submissions(
        ImagePath $imagePath,
        SubmissionRepository $submissionRepository,
        Season $season,
        #[MapQueryString]
        SeasonQueryFilterRequestDto $queryFilter,
    ): Response {
        $results = $submissionRepository->findBySeasonAndFilter(
            $season,
            $queryFilter,
            25,
        );

        return $this->json(\array_map(
            static fn(Submission $submission): AdministrationSubmissionListResponseDto => AdministrationSubmissionListResponseDto::fromSubmission(
                $submission,
                $imagePath,
            ),
            \iterator_to_array($results),
        ));
    }

    #[OA\Get(description: 'Retrieve all past seasons', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'The running season',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    ref: new Model(type: SeasonWithoutSubmissionsSchema::class),
                ),
            ),
        ),
    ])]
    #[Route(
        '/api/season/past',
        name: 'api_season_index_past',
        methods: ['GET'],
    )]
    public function indexPast(ImagePath $imagePath): Response
    {
        return $this->json(\array_map(
            static fn(Season $season): SeasonIndexResponseDto => $season->toResponseObject(
                $imagePath,
            ),
            $this->seasonRepository->findPast(),
        ));
    }

    #[OA\Get(
        description: 'Retrieve a Season by ID',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'The Season',
                content: new OA\JsonContent(
                    ref: new Model(type: SeasonWithoutSubmissionsSchema::class),
                ),
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
        return $this->json($season->toResponseObject($this->imagePath));
    }

    #[OA\Get(description: 'Retrieve all Seasons', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'All Seasons',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    ref: new Model(type: SeasonIndexResponseDto::class),
                ),
            ),
        ),
    ])]
    #[Route('/api/season', name: 'api_season_index', methods: ['GET'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        return $this->json(\array_map(
            fn(SeasonIndexDto $season): SeasonIndexResponseDto => $season->toResponseObject($this->imagePath),
            $this->seasonRepository->findOrdered($user),
        ));
    }
}
