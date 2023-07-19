<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiRoute;
use App\Repository\ActivityRepository;
use App\Repository\FacultySummaryRepository;
use App\Repository\UserSummaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SummaryController extends AbstractController
{
    public function __construct(private FacultySummaryRepository $facultySummaryRepository, private UserSummaryRepository $userSummaryRepository, private SerializerInterface $serializer)
    {
    }

    #[ApiRoute(
        '/api/summary/distances',
        name: 'api_summary_distances',
        methods: ['GET'],
        documentation: 'Get distance sums for all activities',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Retrieved count of User entities',
                'response' => [
                    'activity_name' => 'integer'
                ]
            ]
        ]
    )]
    public function getAllSummaryDistances(ActivityRepository $activityRepository): Response
    {
        $activities = $activityRepository->findAll();

        $summary = [];

        foreach($activities as $activity) {
            $summary[$activity->getName()] = 0;

            foreach($activity->getFacultySummaries() as $facultySummary) {
                $summary[$activity->getName()] += $facultySummary->getDistance();
            }
        }

        return $this->json($summary);
    }
}