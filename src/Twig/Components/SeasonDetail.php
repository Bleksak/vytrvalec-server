<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Dto\SeasonResult\SeasonResultRankDto;
use App\Dto\SeasonResultWithUsersDto;
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

    public SeasonResultRankDto $ranking;

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
        SeasonResultWithUsersDto $seasonResult,
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
    }
}
