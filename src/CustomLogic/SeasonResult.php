<?php

namespace App\CustomLogic;

use App\Dto\ActivityResultDto;
use App\Dto\ExtraPointsDto;
use App\Dto\FacultyResultDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

class SeasonResult
{
    public function __construct(
        private readonly EntityManagerInterface $entityManagerInterface,
        private readonly DailyDistanceExtraPoints $dailyDistanceExtraPoints,
        private readonly WeeklyDistanceExtraPoints $weeklyDistanceExtraPoints,
        private readonly WeeklyElevationExtraPoints $weeklyElevationExtraPoints,
    ) {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function calculateWeek(Season $season, int $week): array
    {
        $query = $this->entityManagerInterface->getConnection()->prepare('
            SELECT SUM(s.distance) AS distance, u.faculty_id AS faculty_id, s.activity_id AS activity_id
            FROM submission s
            INNER JOIN user u ON s.user_id = u.id
            WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
            GROUP BY activity_id, faculty_id
            ORDER BY activity_id ASC, distance DESC;
        ');

        $query->bindValue(1, $week);
        $query->bindValue(2, true);
        $query->bindValue(3, $season->getId());

        return $query->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<int, WeeklyResultDto>
     */
    public function calculate(Season $season): array
    {
        $weeks = $season->getWeekCount();
        $results = [];

        $extraPointsClasses = [$this->dailyDistanceExtraPoints, $this->weeklyDistanceExtraPoints, $this->weeklyElevationExtraPoints];

        for ($i = 0; $i < $weeks; ++$i) {
            $weeklyResult = $this->calculateWeek($season, $i);
            $activities = [];
            foreach ($weeklyResult as $result) {
                if (!array_key_exists($result['activity_id'], $activities)) {
                    $activities[$result['activity_id']] = [];
                }

                $activities[$result['activity_id']][] = new FacultyResultDto($result['faculty_id'], $result['distance']);
            }

            $activityResult = [];

            foreach ($activities as $activityId => $activity) {
                $activityResult[$activityId] = new ActivityResultDto($activityId, $activity);
            }

            if (!empty($activityResult)) {
                $results[$i] = new WeeklyResultDto($i, $activityResult);
            }
        }

        foreach ($extraPointsClasses as $cls) {
            $extras = $cls->calculate($season);
            foreach ($extras as $extra) {
                $results[$cls->getWeek()]->activities[$extra['activity_id']]->extras[] = new ExtraPointsDto($extra['user_id'], $extra['faculty_id'], $cls->getUniqueName(), $extra['value'], $cls->reward());
            }
        }

        $results = array_values($results);

        foreach ($results as $key => $result) {
            $result->activities = array_values($result->activities);
        }

        return $results;
    }
}
