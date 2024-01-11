<?php

namespace App\Action;

use App\CustomLogic\SeasonResult;
use App\Dto\SeasonDto;
use App\Entity\Season;
use App\Repository\SeasonRepository;

class SeasonActions
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly SeasonResult $seasonResult,
    ) {
    }

    public function create(SeasonDto $seasonDto): int
    {
        $season = new Season($seasonDto->start, $seasonDto->end, $seasonDto->charity);

        $this->seasonRepository->save($season, true);

        return $season->getId();
    }

    // TODO: delete
    public function cacheResults(Season $season): void
    {
        $weeklyResults = $this->seasonResult->calculate($season);

        foreach ($weeklyResults as $week => $activityResults) {
            foreach ($activityResults as $activityResult) {
                $activity = $activityResult->activity;

                foreach ($activityResult->results as $facultyResult) {
                    // $facultyResult->faculty
                    // $facultyResult->distance
                }
            }
        }
    }
}
