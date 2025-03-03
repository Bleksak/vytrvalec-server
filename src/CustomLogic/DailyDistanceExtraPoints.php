<?php

namespace App\CustomLogic;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

final class DailyDistanceExtraPoints implements ExtraPoints
{
    public function __construct(
        private readonly EntityManagerInterface $entityManagerInterface,
    ) {
    }

    public static function getUniqueName(): string
    {
        return 'daily_distance';
    }

    public static function getWeek(): int
    {
        return 2;
    }

    public function calculate(Season $season): array
    {
        $query = $this->entityManagerInterface->getConnection()->prepare('
            WITH
                sub AS (
                    SELECT SUM(s.distance) as value, s.activity_id as activity_id, s.user_id as user_id, s.date
                    FROM submission s
                    WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
                    GROUP BY s.date, s.user_id, s.activity_id
                ),
                sorted AS (
                    SELECT *, ROW_NUMBER() OVER (
                    PARTITION BY activity_id
                        ORDER BY value DESC
                    )
                    AS row_num
                    FROM sub
                )
            SELECT value, activity_id, user_id, faculty_id
            FROM sorted s
            INNER JOIN user u ON u.id = s.user_id
            WHERE s.row_num = 1
        ');

        $query->bindValue(1, self::getWeek());
        $query->bindValue(2, true);
        $query->bindValue(3, $season->getId());

        return $query->executeQuery()->fetchAllAssociative();
    }

    public static function reward(): int
    {
        return 1;
    }
}
