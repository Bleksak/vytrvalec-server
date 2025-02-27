<?php

namespace App\CustomLogic;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

final class WeeklyElevationExtraPoints implements ExtraPoints
{
    public function __construct(private readonly EntityManagerInterface $entityManagerInterface)
    {
    }

    public static function getUniqueName(): string
    {
        return 'weekly_elevation';
    }

    public static function getWeek(): int
    {
        return 3;
    }

    public function calculate(Season $season): array
    {
        $query = $this->entityManagerInterface->getConnection()->prepare('
            WITH
                sub AS (
                    SELECT MAX(s.elevation) as value, a.min_elevation, s.activity_id as activity_id, s.user_id as user_id
                    FROM submission s
                    INNER JOIN activity a ON s.activity_id = a.id
                    WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
                    GROUP BY s.user_id, s.activity_id, a.min_elevation
                    HAVING(value) >= min_elevation
                ),
                sorted AS (
                    SELECT *, ROW_NUMBER() OVER (
                    PARTITION BY activity_id
                        ORDER BY value DESC
                    )
                    AS row_num
                    FROM sub
                )
            SELECT value, activity_id, user_id, faculty_id FROM sorted s
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
