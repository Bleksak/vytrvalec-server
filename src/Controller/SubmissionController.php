<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Season;
use App\Entity\User;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Requests\SubmissionRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubmissionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private SubmissionRepository $submissionRepository) {}

    #[Route('/api/submissions/{season}', name: 'api_submissions_season', methods: ['GET'])]
    public function submissionList(Season $season, SubmissionRepository $repository) {
        return $this->json('asdf');
    }

    private function updateSubmissionState($id, $state) {
        $submission = $this->submissionRepository->find($id);

        if ($submission == null) {
            return $this->json(
                ['success' => false]
            );
        }

        $submission->setAccepted($state);

        $this->submissionRepository->persist($submission);
        $this->submissionRepository->flush();

        return $this->json(
            ['success' => true]
        );
    }

    #[IsGranted('ROLE_STAFF')]
    #[Route('/api/submission/accept', name:'api_season_accept', methods: ['POST'])]
    public function acceptSubmission(Request $request) {
        return $this->updateSubmissionState($request->get('id'), true);
    }

    #[IsGranted('ROLE_STAFF')]
    #[Route('/api/submission/reject', name:'api_season_reject', methods: ['POST'])]
    public function rejectSubmission(Request $request) {
        return $this->updateSubmissionState($request->get('id'), false);
    }
    
    #[Route('/api/submission/upload', name: 'api_submission_upload', methods: ['GET'])]
    public function uploadSubmission(UserInterface $user, SubmissionRequest $request) {
        // $category = $this->em->getRepository(Category::class)->find($request->category);
        // if($category == null) {
        //     return $this->json([
        //         'success' => false,
        //         'message' => 'bad_category'
        //     ]);
        // }
        
        
        
    }
    
    #[Route('/submission/upload', name: 'submission_upload', methods: ['GET'])]
    public function uploadSubmissionForm(UserInterface $userInterface): Response
    {
        return $this->render('submission/upload.html.twig', []);
    }
}