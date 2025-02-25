<?php

namespace App\Controller\ApiResource;

use App\Action\StatsActions;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class StatsController extends AbstractController
{
    public function __construct(
        private readonly StatsActions $action,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[Route(
        '/api/stats/total',
        name: 'stats_index',
        methods: ['GET'],
        // documentation: 'Retrieve overall statistics',
        // responses: [
        //   Response::HTTP_OK => [
        //     'message' => 'Statistics retrieved successfully',
        //   ],
        // ],
    )]
    public function indexTotalStatistics(): Response
    {
        return $this->json($this->action->getTotalStatistics());
    }

    #[Route(
        '/api/stats/{user}',
        name: 'stats_user_index',
        methods: ['GET'],
        // documentation: 'Retrieve user statistics',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Statistics retrieved successfully',
        //     ],
        // ],
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
