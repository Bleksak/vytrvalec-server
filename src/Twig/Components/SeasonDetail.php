<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Dto\SeasonResultDto;
use App\Entity\Faculty;
use App\Entity\Season;
use App\Services\SeasonResultRankingService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SeasonDetail
{
    public Season $season;
    public string $title;
    public string $heading;
    public array $ranking;

    public int $totalDistance;

    /** @var array<int, Faculty> */
    public array $faculties;

    public function __construct(
        private SeasonResultRankingService $seasonResultRankingService,
    ) {}

    /**
     * @param array<int, Faculty> $faculties
     */
    public function mount(
        Season $season,
        SeasonResultDto $seasonResult,
        array $faculties,
        string $title = 'season_detail.title',
        string $heading = '',
    ): void {
        $this->title = $title;
        $this->season = $season;
        $this->heading = $heading;
        $this->faculties = $faculties;

        $this->ranking = $this->seasonResultRankingService->calculateSeasonResultRanking(
            $season,
            $seasonResult,
        );

        $this->totalDistance = \array_reduce(
            $this->ranking,
            static fn(int $carry, array $row): int => $row['distance'] + $carry,
            0,
        );
    }
}
