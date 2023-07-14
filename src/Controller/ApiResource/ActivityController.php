<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Activity')]
class ActivityController extends AbstractController
{
    #[ApiRoute(
        '/api/activity/list',
        name: 'activities',
        methods: ['GET'],
        documentation: 'Retrieve all <code>Activity</code> entries',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully retrieved'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access']
        ],
    )]
    #[IsGranted('ROLE_USER')]
    public function activityList(ActivityRepository $activityRepository, SerializerInterface $serializer): Response
    {
        return $this->json($serializer->normalize($activityRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['submissions']
        ]));
    }
}
