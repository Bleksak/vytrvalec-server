<?php

namespace App\Controller\ApiResource;

use App\Action\ActivityActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Requests\ActivityCreateRequest;
use App\Requests\ActivityUpdateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[ApiResource('Activity')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityActions $action,
    )
    {
    }

    #[ApiRoute(
        '/api/activity',
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

        $activity = new Activity($request->getName(), $request->getMinElevation());
        $this->activityRepository->save($activity, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity/{activity}',
        name: 'activity_delete',
        methods: ['DELETE'],
        documentation: 'Deletes an existing <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully deleted'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
            Response::HTTP_NOT_FOUND => ['message' => 'Not found'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Activity $activity): Response
    {
        // TODO: check if deletable(has no submissions)

        $this->activityRepository->remove($activity, true);
        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity/{activity}',
        name: 'activity_patch',
        methods: ['PATCH'],
        documentation: 'Updates an <code>Activity</code> entry',
        responses: [
            Response::HTTP_OK => ['message' => 'Successfully patched'],
            Response::HTTP_FORBIDDEN => ['message' => 'Unauthorized access'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function update(Activity $activity, ActivityUpdateRequest $request): Response
    {
        $activity->setName($request->getName() ?? $activity->getName());
        $activity->setActive($request->getActive() ?? $activity->isActive());
        $activity->setMinElevation($request->getMinElevation() ?? $activity->getMinElevation());

        $this->activityRepository->save($activity, true);
        return new Response(status: Response::HTTP_OK);
    }

    #[ApiRoute(
        '/api/activity',
        name: 'activity_index',
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
            AbstractNormalizer::GROUPS => ['fetchActivity'],
        ]));
    }

}
