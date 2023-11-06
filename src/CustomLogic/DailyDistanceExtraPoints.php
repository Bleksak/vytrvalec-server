<?php

namespace App\CustomLogic;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

class DailyDistanceExtraPoints implements ExtraPoints
{
    public function __construct(private readonly EntityManagerInterface $entityManagerInterface)
    {
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
            SELECT MAX(su) as distance_sum, activity_id, user_id, activity_id
                FROM (
                    SELECT SUM(s.distance) as su, s.activity_id as activity_id, s.user_id as user_id, s.date
                        FROM submission s
                        WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
                        GROUP BY s.date, s.user_id, s.activity_id
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
