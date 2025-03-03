<?php

namespace App\Controller\ApiResource;

use App\Action\SeasonCacheActions;
use App\Entity\Season;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Season Cache')]
final class SeasonCacheController extends AbstractController
{
    public function __construct(
        private readonly SeasonCacheActions $action,
    ) {
    }

    #[OA\Get(
        description: 'Cache season results',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Cache successfully created',
            ),
        ]
    )]
    #[Route(
        '/api/cache/season/{season}',
        name: 'api_cache_season',
        methods: ['GET'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function cacheSeason(Season $season): Response
    {
        if ($season->isRunning()) {
            return $this->json(['season' => 'still_running'], Response::HTTP_BAD_REQUEST);
        }

        $this->action->cacheSeason($season);

        return new Response(status: Response::HTTP_CREATED);
    }
}
