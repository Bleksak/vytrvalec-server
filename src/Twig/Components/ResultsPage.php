<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Dto\SeasonResult\SeasonResultRankDto;
use App\Dto\SeasonResultWithUsersDto;
use App\Dto\Statistics\UserCountGroupedByFacultyTotal;
use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\Season;
use App\Repository\UserRepository;
use App\Services\SeasonResultRankingService;
use App\Utils\AbstractProperty;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ResultsPage
{
    use DefaultActionTrait;

    public Season $currentSeason;

    public SeasonResultWithUsersDto $currentSeasonResult;

    public SeasonResultRankDto $seasonResultRanking;

    public UserCountGroupedByFacultyTotal $userFacultyStatistics;

    /** @var non-empty-array<int, Activity> */
    #[LiveProp(writable: false)]
    public array $activities;

    /** @var non-empty-array<int, Faculty> */
    #[LiveProp(writable: false)]
    public array $faculties;

    /** @var non-empty-list<Season> */
    #[LiveProp(writable: false)]
    public array $seasonList;

    /** @var non-negative-int */
    #[LiveProp(writable: true)]
    public int $currentSeasonIndex = 0;

    #[LiveProp(writable: true)]
    public int $currentActivityIndex = 0;

    #[LiveProp(writable: true)]
    public int $currentWeekIndex = 0;

    #[LiveProp(writable: false, useSerializerForHydration: true)]
    public Chart $chart;

    public function __construct(
        private SeasonResultRankingService $seasonResultRankingService,
        private UserRepository $userRepository,
        private ChartBuilderInterface $chartBuilderInterface,
        private TranslatorInterface $translator,
    ) {}

    /**
     * @param non-empty-array<int, Faculty> $faculties
     * @param non-empty-array<int, Activity> $activities
     * @param non-empty-list<Season> $seasonList
     */
    public function mount(
        array $faculties,
        array $activities,
        array $seasonList,
    ): void {
        $this->faculties = $faculties;
        $this->seasonList = $seasonList;
        $this->activities = $activities;

        $this->recalculateResult();
    }

    #[PreReRender]
    public function recalculateResult(): void
    {
        $newSeason = $this->seasonList[$this->currentSeasonIndex];

        if (
            !AbstractProperty::isInitialized($this, 'currentSeason')
            || $newSeason !== $this->currentSeason
        ) {
            $this->currentSeason = $newSeason;
            $this->currentSeasonResult = $this->seasonResultRankingService->getSeasonResult($this->currentSeason);
            $this->userFacultyStatistics =
                $this->userRepository->countUserGroupedByFaculties($newSeason);
        }

        $this->recalculateRanking();
    }

    public function recalculateRanking(): void
    {
        $this->seasonResultRanking = $this->seasonResultRankingService->calculateSeasonResultRanking(
            $this->currentSeason,
            $this->currentSeasonResult,
            $this->currentActivityIndex === 0
                ? null
                : $this->currentActivityIndex,
            $this->currentWeekIndex === 0 ? null : $this->currentWeekIndex - 1,
        );

        $this->chart = $this->chartBuilderInterface->createChart(Chart::TYPE_BAR);

        $labels = [];
        $colors = [];
        $dataset = [];

        foreach ($this->seasonResultRanking->rows as $row) {
            $faculty = $this->faculties[$row->faculty];
            $labels[] = $faculty->shortcut;
            $colors[] = $faculty->color;
            $dataset[] = $row->distance / 1000;
        }

        $this->chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->translator->trans('results.distance'),
                    'data' => $dataset,
                    'backgroundColor' => $colors,
                ],
            ],
        ]);

        $this->chart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);
    }
}
