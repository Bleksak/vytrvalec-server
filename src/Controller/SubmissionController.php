<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\SubmissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SubmissionController extends AbstractController
{
    #[Route('/api/submissions/{season}')]
    public function submissionList(Season $season, SubmissionRepository $repository) {
        // $repository->findBy([''])
        return $this->json('asdf');
    }
}