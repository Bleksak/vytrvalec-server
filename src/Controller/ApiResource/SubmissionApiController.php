<?php

namespace App\Controller\ApiResource;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Activity;
use App\Entity\FacultySummary;
use App\Entity\Submission;
use App\Entity\User;
use App\Entity\UserSummary;
use App\Repository\FacultySummaryRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserSummaryRepository;
use App\Requests\SubmissionRequest;
use App\Requests\SubmissionStateRequest;
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Imagick;
use ImagickException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[ApiResource('Submission')]
class SubmissionApiController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly SubmissionRepository $submissionRepository)
    {
    }

    #[ApiRoute(
        '/api/submission/create',
        name: 'api_submission_create',
        methods: ['POST'],
        documentation: 'Creates a new <code>Submission</code> entity',
        responses: [
            Response::HTTP_CREATED => [
                'message' => 'Submission created successfully',
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Bad request ',
                'response' => [
                    'distance' => 'err_negative_value',
                    'elevation' => 'err_zero_value'
                ]
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR => [
                'message' => 'Error when processing image'
            ]
        ],
        requestScheme: [
            'distance' => 'integer',
            'elevation' => 'integer',
            'image' => 'file',
            'activity' => 'integer'
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function create(#[CurrentUser] User $user, SubmissionRequest $request, Request $httpRequest, SeasonRepository $seasonRepository): Response
    {
        $errors = $request->validate();
        if(!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $submission = new Submission();
        $now = new DateTimeImmutable();

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->lt('start', $now));
        $criteria->andWhere(Criteria::expr()->gte('end', $now));

        $season = $seasonRepository->matching($criteria)->first();

        if(!$season) {
            return $this->json(['no_season'], Response::HTTP_BAD_REQUEST);
        }

        $submission->setSeason($season);
        $submission->setElevation($request->getElevation());
        $submission->setDistance($request->getDistance());

        $submission->setUser($user);
        $submission->setActivity($request->getActivity());
        $submission->setReviewed(false);
        $submission->setAccepted(false);
        $submission->setDate(new DateTimeImmutable());

        $uniquePath = uniqid('/uploads/') . '.jpg';
        $absolutePath = $httpRequest->server->get('DOCUMENT_ROOT') . $uniquePath;

        try {
            $img = new Imagick($request->getImage()->getRealPath());
            $profiles = $img->getImageProfiles("icc");

            $img->stripImage();
            if(!empty($profiles)) {
                $img->profileImage("icc", $profiles['icc']);
            }

            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (ImagickException) {
            return new Response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $submission->setImage($uniquePath);

        $this->em->persist($submission);
        $this->em->flush();

        return new Response(status: Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/submission/list/{page}',
        name: 'api_submission_list',
        methods: ['GET'],
        documentation: 'Retrieves all submissions',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully retrieved all submissions'
            ]
        ]
    )]
    public function list(int $page): Response
    {
        return $this->json($this->submissionRepository->findBy([], limit: 50, offset: ($page-1) * 50));
    }

    #[ApiRoute(
        '/api/submission/{submission}/delete',
        name: 'api_submission_delete',
        methods: ['DELETE'],
        documentation: 'Deletes a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully deleted'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Cannot delete'
            ]
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function delete(#[CurrentUser] User $user, Submission $submission): Response
    {
        if(!$user->hasRole('ROLE_STAFF') && $user !== $submission->getUser()) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        if($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->em->remove($submission);
        $this->em->flush();

        return new Response(status: Response::HTTP_OK);
    }

    private function setState(Submission $submission, bool $state): void
    {
        $submission->setAccepted($state);
        $submission->setReviewed(true);
    }

    #[ApiRoute(
        '/api/submission/{submission}/accept',
        name: 'api_submission_accept',
        methods: ['POST'],
        documentation: 'Accepts a <code>Submission</code> entity',
        responses: [
            Response::HTTP_OK => [
                'message' => 'Successfully accepted'
            ],
            Response::HTTP_FORBIDDEN => [
                'message' => 'Unauthorized access',
            ],
            Response::HTTP_BAD_REQUEST => [
                'message' => 'Cannot accept'
            ]
        ]
    )]
    #[IsGranted('ROLE_STAFF')]
    public function accept(Submission $submission, FacultySummaryRepository $facultySummaryRepository, UserSummaryRepository $userSummaryRepository): Response
    {
        if($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->setState($submission, true);

        $user = $submission->getUser();
        $faculty = $user->getFaculty();
        $season = $submission->getSeason();

        $facultySummary = $facultySummaryRepository->findOneBy(['faculty' => $faculty, 'season' => $season]);
        $userSummary = $userSummaryRepository->findOneBy(['user' => $user, 'season' => $season]);

        if($facultySummary == null) {
            $facultySummary = new FacultySummary();

            $facultySummary->setFaculty($faculty);
            $facultySummary->setSeason($season);
            $facultySummary->setDistance(0);
            $facultySummary->setElevation(0);
        }

        $facultySummary->setDistance( $facultySummary->getDistance() + $submission->getDistance() );
        $facultySummary->setElevation($facultySummary->getElevation() + $submission->getElevation());

        if($userSummary == null) {
            $userSummary = new UserSummary();

            $userSummary->setUser($user);
            $userSummary->setSeason($season);
            $userSummary->setDistance(0);
            $userSummary->setElevation(0);
        }

        $userSummary->setDistance( $userSummary->getDistance() + $submission->getDistance() );
        $userSummary->setElevation($userSummary->getElevation() + $submission->getElevation());

        $facultySummaryRepository->save($facultySummary);
        $userSummaryRepository->save($userSummary);

        $this->submissionRepository->save($submission, true);

        return new Response(status: Response::HTTP_OK);
    }

    #[Route('/api/submission/{submission}/reject', name: 'api_submission_reject', methods: ['PUT'])]
    #[IsGranted('ROLE_STAFF')]
    public function reject(Submission $submission): Response
    {
        if($submission->isReviewed()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $this->setState($submission, false);
        $this->submissionRepository->save($submission, true);

        return new Response(status: Response::HTTP_OK);
    }
}