<?php

namespace App\Controller\Api;

use App\Entity\Charity;
use App\Entity\Season;
use App\Repository\CharityRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeasonApiController extends AbstractController
{
    public function __construct(private SeasonRepository $seasonRepository)
    {
    }

    #[Route('/api/season/create', name: 'api_season_create', methods: ['GET'])]
    public function create(EntityManagerInterface $em, CharityRepository $charityRepository): Response
    {
        $season = new Season();
        $now = new \DateTimeImmutable();
        $season->setStart($now);
        $end = $now->add(new \DateInterval('P4W'));
        $season->setEnd($end);

        $charity = new Charity();
        $charity->setName('Sbirka na nohu Kasparovy');
        $charity->setDescription('Mila divenka, jela na vylet do bradavic na koštěti. Koště jí ujelo z mezinoží a napálila do orka. Ork jí zlomil pravou nohu na více způsobů.');

        $charityRepository->save($charity);

        $season->setCharity($charity);

        $this->seasonRepository->save($season, true);

        return $this->json([
            'success' => true
        ]);
    }
}