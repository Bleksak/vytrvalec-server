<?php

namespace App\Controller\ApiResource;

use App\Action\ActivityActions;
use App\Dto\ActivityDto;
use App\Entity\Activity;
use App\Form\ActivityFormType;
use App\Repository\ActivityRepository;
use App\Validation\FormErrors;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OA\Tag('Activity')]
final class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityActions $action,
    ) {
    }

    #[OA\Post(
        description: 'Create new Activity',
        requestBody: new OA\RequestBody(
            description: 'The new Activity',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: ActivityDto::class)
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity created',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ],
    )]
    #[Route(
        '/api/activity',
        name: 'activity_create',
        methods: ['POST'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(ActivityFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $id = $this->action->create($form->getData());

        return $this->json(['id' => $id], Response::HTTP_OK);
    }

    #[OA\Delete(
        description: 'Delete an activity',
        parameters: [
            new OA\Parameter(
                name: 'activity',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity deleted',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(
        '/api/activity/{activity}',
        name: 'activity_delete',
        methods: ['DELETE'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Activity $activity): Response
    {
        if (!$this->action->delete($activity)) {
            return $this->json([
                'activity' => ['has_submissions'],
            ], Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Patch(
        description: 'Update activity',
        parameters: [
            new OA\Parameter(
                name: 'activity',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'The updated activity',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: ActivityDto::class)
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity deleted',
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(
        '/api/activity/{activity}',
        name: 'activity_patch',
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(Request $request, Activity $activity): Response
    {
        $form = $this->createForm(ActivityFormType::class, null, [
            'method' => $request->getMethod(),
        ]);

        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($activity, $form->getData());

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Get(
        description: 'Retrieve all activities',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Collection of activites',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: Activity::class),
                    ),
                ),
            ),
        ],
    )]
    #[Route(
        '/api/activity',
        name: 'activity_index',
        methods: ['GET'],
    )]
    public function activityList(NormalizerInterface $normalizer): Response
    {
        return $this->json($normalizer->normalize($this->activityRepository->findAll(), null, [
            AbstractNormalizer::GROUPS => ['fetchActivity'],
        ]));
    }
}
