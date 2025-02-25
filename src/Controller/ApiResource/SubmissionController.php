<?php

namespace App\Controller\ApiResource;

use App\Action\SubmissionActions;
use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Form\SubmissionForm;
use App\Form\SubmissionStateFormType;
use App\Repository\RejectedSubmissionMessageRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Validation\FormErrors;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SubmissionController extends AbstractController
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly SubmissionActions $action,
    ) {
    }

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
    public function delete(#[CurrentUser] User $user, Submission $submission): Response
    {
        if (!$user->hasRole('ROLE_STAFF') && $user !== $submission->getUser()) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        if ($submission->isReviewed() && $submission->isAccepted()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->action->delete($submission);

        return new Response(status: Response::HTTP_OK);
    }

    #[Route(
        '/api/submission',
        name: 'api_submission_create',
        methods: ['POST'],
        // documentation: 'Creates a new <code>Submission</code> entity',
        // responses: [
        //     Response::HTTP_CREATED => [
        //         'message' => 'Submission created successfully',
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
        // ]
    )]
    #[IsGranted('ROLE_USER')]
    public function create(
        #[CurrentUser] User $user,
        SeasonRepository $seasonRepository,
        Request $request,
    ): Response {
        $season = $seasonRepository->getCurrent();

        if ($season === null) {
            return $this->json(['season' => ['no_season']], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(SubmissionForm::class);
        $form->submit($request->request->all() + $request->files->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->create($form->getData(), $user, $season);

        if (!empty($errors)) {
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
    public function listSeason(
        Season $season,
        Request $request,
        int $page = 1,
    ): Response {
        $limit = 50;
        $submissions = $this->submissionRepository->findBySeason($season, $page, $limit);
        $pageCount = 1 + intdiv($submissions->count(), $limit);

        $url = $this->getParameter('app_base');

        return $this->json(
            $this->normalizer->normalize(
                [
                    'pages' => $pageCount,
                    'submissions' => $submissions,
                ],
                null,
                [
                    AbstractNormalizer::GROUPS => ['fetchSubmission'],
                    AbstractNormalizer::CALLBACKS => [
                        'season' => fn (Season $object) => $object->getId(),
                        'activity' => fn (Activity $object) => $object->getId(),
                        'faculty' => fn (Faculty $object) => $object->getId(),
                        'image' => fn (string $image) => $url.$image,
                    ],
                ]
            )
        );
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
    public function list(
        #[CurrentUser] User $user,
        RejectedSubmissionMessageRepository $rejectedSubmissionMessageRepository,
        Request $request,
    ): Response {
        $submissions = $rejectedSubmissionMessageRepository->findByUser($user);

        $url = $this->getParameter('app_base');

        foreach ($submissions as &$submission) {
            $submission['image'] = $url.$submission['image'];
        }

        return $this->json($submissions);
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
    public function unresolvedList(Request $request, int $count): Response
    {
        $url = $this->getParameter('app_base');

        return $this->json($this->normalizer->normalize($this->submissionRepository->findUnreviewed($count), null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['fetchSubmission'],
            AbstractNormalizer::CALLBACKS => ['image' => fn (string $image) => $url.$image],
        ]));
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
        Request $request,
    ): Response {
        if ($submission->isAccepted()) {
            return $this->json(['submission' => ['accepted']], Response::HTTP_BAD_REQUEST);
        }

        // 1. uzivatel da edit, admin vidi starou verzi
        // 2. chceme, aby admin dostal error, ze vidi starou verzi a musi to zkontrolovat znovu

        if ($user !== $submission->getUser()) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(SubmissionForm::class, null, [
            'method' => $request->getMethod(),
        ]);

        $form->submit($request->request->all() + $request->files->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $dto = $form->getData();

        $errors = $this->action->update($submission, $dto);
        if (!empty($errors)) {
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
    public function setState(Submission $submission, Request $request): Response
    {
        $form = $this->createForm(SubmissionStateFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->action->setState($submission, $form->getData());

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return $this->json($submission->getUpdatedAt());
    }
}
