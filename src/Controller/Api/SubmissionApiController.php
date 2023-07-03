<?php

namespace App\Controller\Api;

use App\Entity\Submission;
use App\Entity\User;
use App\Repository\SubmissionRepository;
use App\Requests\SubmissionRequest;
use Doctrine\ORM\EntityManagerInterface;
use Imagick;
use ImagickException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SubmissionApiController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly SubmissionRepository $submissionRepository)
    {
    }

    #[Route('/api/submission/create', name: 'api_submission_create', methods: ['POST'])]
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
        dd($this->json($this->submissionRepository->findAll()));
        return $this->json($this->submissionRepository->findAll());
    }
    public function delete()
    {

    }

    public function accept()
    {

    }

    public function reject()
    {

    }
}