<?php

namespace App\Controller\ApiResource;

use App\Action\ActivityActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Activity;
use App\Form\ActivityFormType;
use App\Repository\ActivityRepository;
use App\Validation\FormErrors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ApiResource('Activity')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityActions $action,
    ) {
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
    public function create(Request $request): Response
    {
        $form = $this->createForm(ActivityFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $id = $this->action->create($form->getData());

        return $this->json(['id' => $id], Response::HTTP_OK);
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
        if (!$this->action->delete($activity)) {
            return $this->json([
                'activity' => ['has_submissions']
            ], Response::HTTP_BAD_REQUEST);
        }

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
    public function updatePatch(Request $request, Activity $activity): Response
    {
        $form = $this->createForm(ActivityFormType::class, null, [
            'method' => $request->getMethod(),
        ]);

        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($activity, $form->getData());

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
    public function activityList(NormalizerInterface $normalizer): Response
    {
        return $this->json($normalizer->normalize($this->activityRepository->findAll(), null, [
            AbstractNormalizer::GROUPS => ['fetchActivity'],
        ]));
    }
}
