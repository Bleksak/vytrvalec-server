<?php

namespace App\Controller\ApiResource;

use App\Action\StatsActions;
use App\Dto\TotalStatisticsDto;
use App\Entity\ProfileCache;
use App\Entity\User;
use App\Schema\ProfileCacheSchema;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OA\Tag(name: 'Statistics')]
final class StatsController extends AbstractController
{
    public function __construct(
        private readonly StatsActions $action,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[OA\Get(
        description: 'Retrieve all statistics',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Collection of statistics',
                content: new OA\JsonContent(
                    ref: new Model(type: TotalStatisticsDto::class)
                ),
            ),
        ],
    )]
    #[Route(
        '/api/stats/total',
        name: 'stats_index',
        methods: ['GET'],
    )]
    public function indexTotalStatistics(): Response
    {
        return $this->json($this->action->getTotalStatistics());
    }

    #[OA\Get(
        description: 'Retrieve the user profile statistics',
        responses: [
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
        ]
    )]
    #[Route(
        '/api/stats/{user}',
        name: 'stats_user_index',
        methods: ['GET'],
    )]
    public function indexUserStatistics(?User $user = null): Response
    {
        if ($user === null) {
            /**
             * @var ?User $user
             */
            $user = $this->getUser();
        }

        return $this->json($this->normalizer->normalize($user?->getProfileCaches(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
        ]));
    }
}
