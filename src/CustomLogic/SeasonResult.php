<?php

namespace App\CustomLogic;

use App\Dto\ActivityResultDto;
use App\Dto\ExtraPointsDto;
use App\Dto\FacultyResultDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Repository\SubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SeasonResult
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly DailyDistanceExtraPoints $dailyDistanceExtraPoints,
        private readonly WeeklyDistanceExtraPoints $weeklyDistanceExtraPoints,
        private readonly WeeklyElevationExtraPoints $weeklyElevationExtraPoints,
    ) {
    }

    /**
     * @return array<int, WeeklyResultDto>
     */
    public function calculate(Season $season): array
    {
        $weeks = $season->getWeekCount();
        $results = [];

        /**
         * @var array<ExtraPoints>
         */
        $extraPointsClasses = [
            $this->dailyDistanceExtraPoints,
            $this->weeklyDistanceExtraPoints,
            $this->weeklyElevationExtraPoints,
        ];

        for ($i = 0; $i < $weeks; ++$i) {
            $weeklyResult = $this->submissionRepository->getResultsForWeek($season, $i);
            $activities = [];

            foreach ($weeklyResult as $result) {
                if (!array_key_exists($result->activity, $activities)) {
                    $activities[$result->activity] = [];
                }

                $activities[$result->activity][] = new FacultyResultDto(
                    $result->faculty,
                    $result->distance
                );
            }

            $activityResult = [];

            foreach ($activities as $activityId => $activity) {
                $activityResult[$activityId] = new ActivityResultDto(
                    $activityId,
                    $activity
                );
            }

            if (!empty($activityResult)) {
                $results[$i] = new WeeklyResultDto(
                    $i,
                    $activityResult
                );
            }
        }

        foreach ($extraPointsClasses as $cls) {
            $extras = $cls->calculate($season);
            foreach ($extras as $extra) {
                $results[$cls->getWeek()]->activities[$extra['activity_id']]->extras[] = new ExtraPointsDto(
                    $extra['user_id'],
                    $extra['faculty_id'],
                    $cls->getUniqueName(),
                    $extra['value'],
                    $cls->reward()
                );
            }
        }

        $results = array_values($results);

        foreach ($results as $key => $result) {
            $result->activities = array_values($result->activities);
        }

        return $results;
    }
}
