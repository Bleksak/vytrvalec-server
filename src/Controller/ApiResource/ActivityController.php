<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\ActivityActions;
use App\Dto\Activity\ActivityCreateDto;
use App\Dto\Activity\ActivityUpdateDto;
use App\Dto\Activity\Response\ActivityResponseDto;
use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Services\ImagePath;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Activity')]
final class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityActions $action,
    ) {}

    #[OA\Post(
        description: 'Create new Activity',
        requestBody: new OA\RequestBody(
            description: 'The new Activity',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: ActivityCreateDto::class),
            ),
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
    #[Route('/api/activity', name: 'activity_create', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function create(
        #[MapRequestPayload] ActivityCreateDto $activityCreateDto,
        ImagePath $imagePath,
    ): Response {
        $activity = $this->action->create($activityCreateDto);

        if ($activity === null) {
            return $this->json([
                'icon' => ['string.'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(
            $activity->toResponseObject($imagePath),
            Response::HTTP_CREATED,
        );
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
        ],
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
                ref: new Model(type: ActivityUpdateDto::class),
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Activity updated',
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
        '/api/activity/{activity}',
        name: 'activity_patch',
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(
        #[MapRequestPayload] ActivityUpdateDto $dto,
        Activity $activity,
    ): Response {
        $this->action->update($activity, $dto);

        return new Response(status: Response::HTTP_OK);
    }

    #[OA\Get(description: 'Retrieve all activities', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Collection of activites',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: new Model(type: Activity::class)),
            ),
        ),
    ])]
    #[Route('/api/activity', name: 'activity_index', methods: ['GET'])]
    public function index(ImagePath $imagePath): Response
    {
        return $this->json(\array_map(
            static fn(Activity $activity): ActivityResponseDto => $activity->toResponseObject(
                $imagePath,
            ),
            $this->activityRepository->findAll(),
        ));
    }
}
