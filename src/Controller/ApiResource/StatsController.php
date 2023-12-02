<?php

namespace App\Controller\ApiResource;

use App\Action\StatsActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[ApiResource('Stats')]
class StatsController extends AbstractController {
    public function __construct(private readonly StatsActions $action)
    {

    }

    public function indexUserStatistics(User $user): Response
    {
        
    }

    #[ApiRoute(
        '/api/stats',
        name: 'stats_index',
        methods: ['PATCH'],
        documentation: 'Retrieve overall statistics',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Statistics retrieved successfully',
            ],
        ],
    )]
    public function indexTotalStatistics(): Response
    {
        return $this->json($this->action->getTotalStatistics());
    }
}
