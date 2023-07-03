<?php

namespace App\Controller\Api;

use App\Entity\Submission;
use App\Entity\User;
use App\Repository\SubmissionRepository;
use App\Requests\SubmissionStateRequest;
use App\Requests\SubmissionRequest;
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

    #[Route('/api/submission/create', name: 'api_submission_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(#[CurrentUser] User $user, SubmissionRequest $request, Request $httpRequest): Response
    {
        $submission = new Submission();
        $submission->setAccepted(false);
        $submission->setElevation($request->getElevation());
        $submission->setDistance($request->getDistance());
        $submission->setUser($user);
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

        $this->em->persist($submission);
        $this->em->flush();
    }

    #[Route('/api/submission/accept', name: 'api_submission_accept', methods: ['PUT'])]
    #[IsGranted('ROLE_STAFF')]
    public function accept(SubmissionStateRequest $request): Response
    {
        $this->setState($request->getSubmission(), true);
        return $request->getResponse(true);
    }

    #[Route('/api/submission/reject', name: 'api_submission_reject', methods: ['PUT'])]
    #[IsGranted('ROLE_STAFF')]
    public function reject(SubmissionStateRequest $request): Response
    {
        $this->setState($request->getSubmission(), false);
        return $request->getResponse(true);
    }

}