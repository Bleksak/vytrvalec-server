<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\ActivityResultDto;
use App\Dto\ExtraPointsDto;
use App\Dto\FacultyResultDto;
use App\Dto\SeasonResultDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Repository\SubmissionRepository;
use DateTime;

final readonly class SeasonResultCalculator
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private DailyDistanceExtraPoints $dailyDistanceExtraPoints,
        private WeeklyDistanceExtraPoints $weeklyDistanceExtraPoints,
        private WeeklyElevationExtraPoints $weeklyElevationExtraPoints,
    ) {}

    public function calculate(Season $season): SeasonResultDto
    {
        $weeks = $season->getWeekCount();
        $results = [];

        /** @var list<ExtraPointsInterface> */
        $extraPointsClasses = [
            $this->dailyDistanceExtraPoints,
            $this->weeklyDistanceExtraPoints,
            $this->weeklyElevationExtraPoints,
        ];

        for ($i = 0; $i < $weeks; ++$i) {
            $weeklyResult = $this->submissionRepository->getResultsForWeek(
                $season,
                $i,
            );
            $activities = [];

            foreach ($weeklyResult as $result) {
                $activities[$result->activity][$result->faculty] =
                    new FacultyResultDto($result->faculty, $result->distance);
            }

            $activityResult = [];

            foreach ($activities as $activityId => $activity) {
                $activityResult[$activityId] = new ActivityResultDto(
                    $activityId,
                    $activity,
                );
            }

            if (\count($activityResult) !== 0) {
                $results[$i] = new WeeklyResultDto($i, $activityResult);
            }
        }

        if ($season->getEnd() > new DateTime('2022-01-01')) {
            foreach ($extraPointsClasses as $cls) {
                $extras = $cls->calculate($season);
                foreach ($extras as $extra) {
                    $results[$cls->getWeek()]->activities[$extra->activityId]->extras[] =
                        new ExtraPointsDto(
                            $extra->user,
                            $extra->facultyId,
                            $cls->getUniqueName(),
                            $extra->value,
                            $cls->reward(),
                        );
                }
            }
        }

        $topThree = $this->submissionRepository->findOutliers($season);

        return new SeasonResultDto($results, $topThree);
    }
}
