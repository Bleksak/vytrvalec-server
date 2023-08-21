<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Requests\ActivityCreateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Activity')]
class ActivityController extends AbstractController
{
    public function __construct(private ActivityRepository $activityRepository)
    {

    }

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
    public function activityList(SerializerInterface $serializer): Response
    {
        return $this->json($serializer->normalize($this->activityRepository->findAll(), null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['submissions']
        ]));
    }

    #[ApiRoute(
        '/api/activity/create',
        name: 'activity_create',
        methods: ['POST'],
        documentation: 'Create a new <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully created'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad request']
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(ActivityCreateRequest $request): Response
    {
        $errors = $request->validate();

        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $activity = new Activity();
        $activity->setActive(true);
        $activity->setName($request->getName());
        $activity->setMinElevation($request->getMinElevation());

        $this->activityRepository->save($activity, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity/{activity}/delete',
        name: 'activity_create',
        methods: ['DELETE'],
        documentation: 'Deletes an existing <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully deleted'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Activity $activity): Response
    {
        $this->activityRepository->remove($activity, true);
        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity/{activity}/enable',
        name: 'activity_enable',
        methods: ['PATCH'],
        documentation: 'Enables an <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully enabled'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function enable(Activity $activity): Response
    {
        $activity->setActive(true);
        $this->activityRepository->save($activity, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity/{activity}/disable',
        name: 'activity_disable',
        methods: ['PATCH'],
        documentation: 'Disables an <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully disabled'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function disable(Activity $activity): Response
    {
        $activity->setActive(false);
        $this->activityRepository->save($activity, true);

        return new Response(status: Response::HTTP_OK);
    }
}
