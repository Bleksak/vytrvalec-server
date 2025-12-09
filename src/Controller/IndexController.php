<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\StatisticsActions;
use App\Repository\FacultyRepository;
use App\Repository\SeasonRepository;
use App\Services\SeasonResultRankingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;

#[Route('/', name: self::ROUTE, methods: [Request::METHOD_GET])]
final class IndexController extends AbstractController
{
    public const string ROUTE = 'index';

    public function __construct(
        private readonly StatisticsActions $statisticsAction,
        private readonly FacultyRepository $facultyRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly SeasonResultRankingService $seasonResultService,
    ) {}

    public function __invoke(LocaleSwitcher $localeSwitcher): Response
    {
        $statistics = $this->statisticsAction->getTotalStatistics($localeSwitcher->getLocale());
        $pastSeasons = $this->seasonRepository->findPast();
        $lastSeason = $pastSeasons[0] ?? null;

        $lastSeasonResult = $lastSeason === null
            ? null
            : $this->seasonResultService->getSeasonResult($lastSeason);

        $faculties = $this->facultyRepository->findAllWithTranslations();

        $currentSeason = $this->seasonRepository->findCurrentSeason();

        return $this->render('index.html.twig', [
            'statistics' => $statistics,
            'faculties' => $faculties,
            'past_seasons' => $pastSeasons,
            'last_season' => $lastSeason,
            'last_season_result' => $lastSeasonResult,
            'current_season' => $currentSeason,
        ]);
    }
}
