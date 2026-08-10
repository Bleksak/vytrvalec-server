<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\StatisticsActions;
use App\Dto\Statistics\ProfileCacheResponseDto;
use App\Dto\TotalStatisticsDto;
use App\Dto\UserCountByFacultyStatistics;
use App\Entity\ProfileCache;
use App\Entity\Season;
use App\Entity\User;
use App\Schema\ProfileCacheSchema;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[OA\Tag(name: 'Statistics')]
final class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly StatisticsActions $action,
    ) {}

    #[OA\Get(description: 'Retrieve all statistics', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Collection of statistics',
            content: new OA\JsonContent(
                ref: new Model(type: TotalStatisticsDto::class),
            ),
        ),
    ])]
    #[Route('/api/stats/total', name: 'stats_index', methods: ['GET'])]
    public function indexTotalStatistics(): Response
    {
        return $this->json($this->action->getTotalStatistics());
    }

    #[OA\Get(
        description: 'Retrieve user count grouped by faculties',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User counts by faculties',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: UserCountByFacultyStatistics::class),
                    ),
                ),
            ),
        ],
    )]
    #[Route(
        '/api/statistics/faculties/{season}',
        name: 'statistics_faculties_index',
        methods: ['GET'],
    )]
    public function indexFacultyStatistics(Season $season): Response
    {
        return $this->json($this->action->getUserCountByFaculties($season));
    }

    #[OA\Get(description: 'Retrieve the user profile statistics', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'User statistics',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    ref: new Model(type: ProfileCacheSchema::class),
                ),
            ),
        ),
    ])]
    #[Route('/api/stats/{user}', name: 'stats_user_index', methods: ['GET'])]
    public function indexUserStatistics(
        #[CurrentUser]
        User $currentUser,
        ?User $user = null,
    ): Response {
        if ($user !== $currentUser) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        $cache = $currentUser->getProfileCaches();

        return $this->json(\array_map(
            static fn(ProfileCache $profileCache): ProfileCacheResponseDto => $profileCache->toResponseObject(),
            $cache?->toArray() ?? [],
        ));
    }
}
