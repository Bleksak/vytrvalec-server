<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\SubmissionActions;
use App\Dto\Extract\ExtractSubmissionDto;
use App\Dto\Submission\Response\SubmissionResponseDto;
use App\Dto\Submission\Response\UnreviewedSubmissionResponseDto;
use App\Dto\Submission\SubmissionCreateDto;
use App\Dto\Submission\SubmissionEditDto;
use App\Dto\Submission\SubmissionStateDto;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Exceptions\User\UserBannedException;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Services\ImagePath;
use App\Utils\FeatureFlag;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Submission')]
final class SubmissionController extends AbstractController
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly SubmissionActions $action,
        private readonly ImagePath $imagePath,
    ) {}

    #[Route(
        path: '/api/submission/{submission}',
        name: 'api_submission_delete',
        methods: ['DELETE'],
        // documentation: 'Deletes a <code>Submission</code> entity',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully deleted',
        //     ],
        //     Response::HTTP_FORBIDDEN => [
        //         'message' => 'Unauthorized access',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Cannot delete',
        //     ],
        // ]
    )]
    #[IsGranted('ROLE_USER')]
    public function delete(
        #[CurrentUser] User $user,
        Submission $submission,
    ): Response {
        if (!$user->hasRole('ROLE_STAFF') && $user !== $submission->user) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        if ($submission->reviewed && $submission->accepted) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->action->delete($submission);

        return new Response(status: Response::HTTP_OK);
    }

    #[Route(
        '/api/submission',
        name: 'api_submission_create',
        methods: ['POST'],
    )]
    #[IsGranted('ROLE_USER')]
    public function create(
        #[CurrentUser] User $user,
        SeasonRepository $seasonRepository,
        #[MapRequestPayload] SubmissionCreateDto $submissionCreateDto,
    ): Response {
        if ($user->banned) {
            throw new UserBannedException();
        }

        $season = $seasonRepository->findCurrentSeason();

        if ($season === null) {
            return $this->json(['season' => [
                'no_season',
            ]], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->create($submissionCreateDto, $user, $season);

        if (\count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_CREATED);
    }

    #[Route(
        '/api/submission/list/{season}/{page}',
        name: 'api_submission_list_season',
        methods: ['GET'],
        // documentation: 'Retrieves all submissions in given Season',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieved all submissions',
        //         'response' => [
        //             'pages' => 'integer',
        //             'submissions' => 'array',
        //         ],
        //     ],
        // ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function listSeason(Season $season, int $page = 1): Response
    {
        $limit = 50;
        $submissions = $this->submissionRepository->findBySeason(
            $season,
            $page,
            $limit,
        );
        $pageCount = 1 + \intdiv($submissions->count(), $limit);

        return $this->json([
            'pages' => $pageCount,
            'submissions' => \array_map(
                fn(Submission $submission): SubmissionResponseDto => $submission->toResponseObject($this->imagePath),
                \iterator_to_array($submissions),
            ),
        ]);
    }

    #[Route(
        '/api/submission/user',
        name: 'api_submission_list',
        methods: ['GET'],
        // documentation: 'Retrieves all submissions for current user',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieved all submissions',
        //     ],
        // ]
    )]
    public function list(#[CurrentUser] User $user): Response
    {
        $submissions = $this->submissionRepository->findAllByUser($user);

        return $this->json(\array_map(
            fn(Submission $submission): SubmissionResponseDto => $submission->toResponseObject($this->imagePath),
            $submissions,
        ));
    }

    #[Route(
        '/api/submission/unresolved/{count}',
        name: 'api_submission_list_unresolved',
        methods: ['GET'],
        // documentation: 'Retrieves some unresolved submissions across all seasons',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully retrieved some unresolved submissions',
        //     ],
        // ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function unresolvedList(int $count): Response
    {
        $submissions = $this->submissionRepository->findUnreviewed($count);

        $userResponse = [];
        $submissionResponse = [];

        foreach ($submissions as $submission) {
            $submissionResponse[] = $submission->toResponseObject($this->imagePath);
            $user = $submission->user;

            $userId = $user->id;

            if (!isset($userResponse[$userId])) {
                $userResponse[$userId] = $user->toResponseObject();
            }
        }

        return $this->json(new UnreviewedSubmissionResponseDto(
            $submissionResponse,
            $userResponse,
        ));
    }

    #[Route(
        '/api/submission/{submission}',
        name: 'api_submission_edit',
        methods: ['PATCH'],
        // documentation: 'Edits a <code>Submission</code> entity',
        // responses: [
        //     Response::HTTP_CREATED => [
        //         'message' => 'Submission edited successfully',
        //     ],
        //     Response::HTTP_FORBIDDEN => [
        //         'message' => 'Unauthorized access',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Bad request ',
        //         'response' => [
        //             'distance' => 'err_negative_value',
        //             'elevation' => 'err_zero_value',
        //         ],
        //     ],
        //     Response::HTTP_INTERNAL_SERVER_ERROR => [
        //         'message' => 'Error when processing image',
        //     ],
        // ],
        // requestScheme: [
        //     'distance' => 'integer',
        //     'elevation' => 'integer',
        //     'image' => 'file',
        //     'activity' => 'integer',
        //     'updated_at' => 'datetime',
        // ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit(
        #[CurrentUser] User $user,
        Submission $submission,
        #[MapRequestPayload] SubmissionEditDto $submissionEditDto,
    ): Response {
        if ($submission->accepted) {
            return $this->json(['submission' => [
                'accepted',
            ]], Response::HTTP_BAD_REQUEST);
        }

        // Uzivatel posle v roce 2024 submission a dostane reject,
        // v roce 2025 se ji pokusi upravit -> zobrazi se v submission pageru
        // -> neni mozne zjistit jestli je z roku 2024/2025
        // -> acceptne se -> zmeni vysledky z predchozich let
        if (!$submission->season->isRunning()) {
            return $this->json(['season' => [
                'no_season',
            ]], Response::HTTP_BAD_REQUEST);
        }

        // 1. uzivatel da edit, admin vidi starou verzi
        // 2. chceme, aby admin dostal error, ze vidi starou verzi a musi to zkontrolovat znovu

        if ($user !== $submission->user) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $errors = $this->action->update($submission, $submissionEditDto);

        if (\count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return new Response(status: Response::HTTP_CREATED);
    }

    #[Route(
        '/api/submission/{submission}/state',
        name: 'api_submission_state',
        methods: ['PATCH'],
        // documentation: 'Accepts/rejects a <code>Submission</code> entity',
        // responses: [
        //     Response::HTTP_OK => [
        //         'message' => 'Successfully changed state',
        //     ],
        //     Response::HTTP_FORBIDDEN => [
        //         'message' => 'Unauthorized access',
        //     ],
        //     Response::HTTP_BAD_REQUEST => [
        //         'message' => 'Cannot set state (invalid values sent)',
        //     ],
        // ],
        // requestScheme: [
        //     'updated_at' => 'datetime',
        //     'state' => 'bool',
        //     'message' => '?string',
        // ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function setState(
        #[CurrentUser] User $user,
        #[MapRequestPayload] SubmissionStateDto $dto,
        Submission $submission,
    ): Response {
        $errors = $this->action->setState($user, $submission, $dto);

        if (\count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return $this->json($submission->updatedAt);
    }

    #[OA\Get(description: 'Extract submission for given seasons', responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Submissions for the season',
            content: new JsonContent(
                ref: new Model(type: ExtractSubmissionDto::class),
            ),
        ),
    ])]
    #[Route(
        '/api/extract/submissions',
        'api_extract_submissions',
        methods: ['GET'],
    )]
    public function extractReviewedSubmissionForSeasons(
        #[CurrentUser] User $user,
        #[MapQueryParameter] ?int $season = null,
    ): Response {
        if (
            !$user->canAccess(FeatureFlag::ROLE_STAFF)
            && !$user->canAccess(FeatureFlag::FEATURE_EXPORT_SUBMISSIONS)
        ) {
            return new Response(status: Response::HTTP_UNAUTHORIZED);
        }

        $extractedData = $this->submissionRepository->extractBySeasons(
            $this->imagePath,
            $season,
        );

        return $this->json($extractedData);
    }
}
