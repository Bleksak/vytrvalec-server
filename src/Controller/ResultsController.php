<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Season\SeasonIndexDto;
use App\Entity\Season;
use App\Repository\ActivityRepository;
use App\Repository\FacultyRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;

#[Route('/results', name: self::ROUTE, methods: [Request::METHOD_GET])]
final class ResultsController extends AbstractController
{
    public const string ROUTE = 'results';

    public function __construct(
        private readonly FacultyRepository $facultyRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly LocaleSwitcher $localeSwitcher,
    ) {}

    public function __invoke(): Response
    {
        $seasonList = \array_map(
            static fn(SeasonIndexDto $seasonIndex): Season => $seasonIndex->season,
            $this->seasonRepository->findOrdered(),
        );

        $activities = $this->activityRepository->findAllWithTranslations($this->localeSwitcher->getLocale());

        // NOTE: Tohle funguje protoze je povoleno aby byla aktivni pouze jedna sezona
        $currentSeason = $seasonList[0]?->isRunning() ? $seasonList[0] : null;

        return $this->render('results.html.twig', [
            'faculties' => $this->facultyRepository->findAllWithTranslations(),
            'activities' => $activities,
            'season_list' => $seasonList,
            'current_season' => $currentSeason,
        ]);
    }
}
