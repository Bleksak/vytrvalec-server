<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SeasonController extends AbstractController
{
    public function __construct(private SeasonRepository $repository) {}

    #[Route('/api/seasons', name: 'api_seasons', methods: ['GET'])]
    public function seasonList(): Response {
        return $this->json($this->repository->findAll());
    }

//    #[Route('/api/season/{season}', name: 'api_season', methods: ['GET'])]
//    public function season(Season $season): Response {
//        return $this->json($season);
//    }
}
