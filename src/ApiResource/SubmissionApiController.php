<?php

namespace App\ApiResource;

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

class SubmissionApiController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly SubmissionRepository $submissionRepository)
    {
    }

    // TODO: delete this
    #[Route('/api/submission/createTest', name: 'api_submission_createTest', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function createTest(#[CurrentUser] User $user, Request $httpRequest, SeasonRepository $seasonRepository): Response
    {
        $submission = new Submission();
        $now = new \DateTimeImmutable();

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->lt('start', $now));
        $criteria->andWhere(Criteria::expr()->gte('end', $now));

        $season = $seasonRepository->matching($criteria)->first();
        if(!$season) {
            return $this->json([
                'NO SEASON KOKOT'
            ]);
        }

        $activity = $this->em->getRepository(Activity::class)->find(1);

        $submission->setElevation(1000);
        $submission->setDistance(1000);
        $submission->setUser($user);
        $submission->setReviewed(false);
        $submission->setAccepted(false);
        $submission->setDate($now);
        $submission->setSeason($season);
        $submission->setActivity($activity);

        $uniquePath = uniqid('/uploads/') . '.jpg';
        $submission->setImage($uniquePath);

        $this->submissionRepository->save($submission, true);

        return $this->json([
            'success' => true
        ]);
    }

    #[Route('/api/submission/create', name: 'api_submission_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(#[CurrentUser] User $user, SubmissionRequest $request, Request $httpRequest, SeasonRepository $seasonRepository): Response
    {
        $submission = new Submission();
        $now = new \DateTimeImmutable();

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->lt('start', $now));
        $criteria->andWhere(Criteria::expr()->gte('end', $now));

        $season = $seasonRepository->matching($criteria)->first();

        if(!$season) {
            return $request->getResponse(false, [
                // TODO: message
                'NEBEZI SEZONA MOREEEE'
            ]);
        }

        $submission->setElevation($request->getElevation());
        $submission->setDistance($request->getDistance());

        $submission->setUser($user);
        $submission->setActivity($request->getActivity());
        $submission->setReviewed(false);
        $submission->setAccepted(false);

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
        } catch (ImagickException $e) {
            return $request->getResponse(false, [$e->getMessage()]);
        }

        $submission->setImage($uniquePath);

        $this->em->persist($submission);
        $this->em->flush();

        return $request->getResponse(true);
    }

    #[Route('/api/submissions/list', name: 'api_submissions_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->json($this->submissionRepository->findAll());
    }

    #[Route('/api/submission/delete', name: 'api_submission_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(#[CurrentUser] User $user, SubmissionStateRequest $request): Response
    {
        $submission = $request->getSubmission();

        if(!$user->hasRole('ROLE_STAFF') && $user !== $submission->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if($submission->isReviewed()) {
            return $request->getResponse(false, [
                'cannot_delete'
            ]);
        }

        $this->em->remove($submission);
        $this->em->flush();

        return $request->getResponse(true);
    }

    private function setState(Submission $submission, bool $state): void
    {
        $submission->setAccepted($state);
        $submission->setReviewed(true);
    }

    #[Route('/api/submission/accept', name: 'api_submission_accept', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function accept(SubmissionStateRequest $request, FacultySummaryRepository $facultySummaryRepository, UserSummaryRepository $userSummaryRepository): Response
    {
        if($request->getSubmission()->isReviewed()) {
            return $request->getResponse(false, [
                // TODO: message
                'NEJDE TOOO, UZ JE REVIEWED BROO'
            ]);
        }

        $this->setState($request->getSubmission(), true);
        $submission = $request->getSubmission();

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

        $this->submissionRepository->save($request->getSubmission(), true);

        return $request->getResponse(true);
    }

    #[Route('/api/submission/reject', name: 'api_submission_reject', methods: ['PUT'])]
    #[IsGranted('ROLE_STAFF')]
    public function reject(SubmissionStateRequest $request): Response
    {
        $this->setState($request->getSubmission(), false);
        $this->submissionRepository->save($request->getSubmission(), true);
        return $request->getResponse(true);
    }

}