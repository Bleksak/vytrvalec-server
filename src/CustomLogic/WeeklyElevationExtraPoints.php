<?php

namespace App\CustomLogic;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

class WeeklyElevationExtraPoints implements ExtraPoints
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
            SELECT MAX(elevation_sum) as value, activity_id, user_id, faculty_id
                FROM (
                    SELECT SUM(s.elevation) as elevation_sum, a.min_elevation, s.activity_id as activity_id, s.user_id as user_id, u.faculty_id as faculty_id
                        FROM submission s
                        INNER JOIN user u ON s.user_id = u.id
                        INNER JOIN activity a ON s.activity_id = a.id
                        WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
                        GROUP BY s.date, s.user_id, s.activity_id
                        HAVING(elevation_sum) >= min_elevation
                ) as sums
            GROUP BY activity_id;
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
