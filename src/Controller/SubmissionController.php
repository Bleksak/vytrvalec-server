<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubmissionController extends AbstractController
{
    public function __construct() {}


    #[Route('/submission/create', name: 'submission_create', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function uploadSubmissionForm(): Response
    {
        return $this->render('base.html.twig', []);
    }
}