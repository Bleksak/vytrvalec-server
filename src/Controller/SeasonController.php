<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SeasonController extends AbstractController
{
    private SeasonRepository $repository;

    public function __construct(SeasonRepository $repository) {
        $this->repository = $repository;
    }

    #[Route('/api/seasons', name: 'api_seasons', methods: ['GET'])]
    public function seasonList(SerializerInterface $serializer): Response {
        $seasons = $serializer->serialize($this->repository->findAll(), 'json');

        $response = new Response($seasons);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    // #[Route('/season', name: 'app_season')]
    // public function index(): Response
    // {
    //     return $this->render('season/index.html.twig', [
    //         'controller_name' => 'SeasonController',
    //     ]);
    // }
}
