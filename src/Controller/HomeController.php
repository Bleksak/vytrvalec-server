<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SeasonRepository $seasonRepository): Response
    {
        $seasons = $seasonRepository->findBy([], ['start' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'seasons' => $seasons
        ]);
    }

    #[Route('/rules', name: 'rules')]
    public function rules(): Response 
    {
        return $this->render('home/rules.html.twig', []);
    }
}
