<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\AnonymizedUser;
use App\Dto\ExtraPointsResultDto;
use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

final class WeeklyDistanceExtraPoints implements ExtraPoints
{
    public function __construct(
        private readonly EntityManagerInterface $entityManagerInterface,
    ) {
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
                    SELECT SUM(s.distance) as value, s.activity_id as activity_id, s.user_id as user_id
                    FROM submission s
                    WHERE s.week = ? AND s.accepted = ? AND s.season_id = ?
                    GROUP BY s.user_id, s.activity_id
                ),
                sorted AS (
                    SELECT *, ROW_NUMBER() OVER (
                    PARTITION BY activity_id
                        ORDER BY value DESC
                    )
                    AS row_num
                    FROM sub
                )
            SELECT value, activity_id, user_id, COALESCE(f.parent_id, u.faculty_id) AS faculty_id, u.first_name, u.last_name, u.accepted_gdpr
            FROM sorted s
            INNER JOIN user u ON u.id = s.user_id
            INNER JOIN faculty f ON u.faculty_id = f.id
            WHERE s.row_num = 1
        ');

        $query->bindValue(1, self::getWeek());
        $query->bindValue(2, true);
        $query->bindValue(3, $season->getId());

        return array_map(
            static fn ($row) => new ExtraPointsResultDto(
                new AnonymizedUser(
                    $row['first_name'],
                    $row['last_name'],
                    $row['accepted_gdpr'],
                ),
                $row['activity_id'],
                $row['faculty_id'],
                (int) $row['value'],
            ),
            $query->executeQuery()->fetchAllAssociative()
        );
    }

    public static function getUniqueName(): string
    {
        return 'weekly_distance';
    }

    public static function reward(): int
    {
        return 2;
    }
}
