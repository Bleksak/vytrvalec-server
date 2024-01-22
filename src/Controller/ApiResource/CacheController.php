<?php

namespace App\Controller\ApiResource;

use App\Action\CacheActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[ApiResource('Cache')]
class CacheController extends AbstractController
{
    public function __construct(private readonly CacheActions $action)
    {
    }

    #[ApiRoute(
        '/api/cache/season/{season}',
        name: 'api_cache_season',
        methods: ['GET'],
        documentation: 'Cache season results',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Successfully cached a season',
            ]
        ],
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
