<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\AnonymizedUser;
use App\Dto\ExtraPointsResultDto;
use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DailyDistanceExtraPoints implements ExtraPointsInterface
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
    ) {
    }

    #[\Override]
    public static function getUniqueName(): string
    {
        return 'daily_distance';
    }

    #[\Override]
    public static function getWeek(): int
    {
        return 2;
    }

    #[\Override]
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
            SELECT value, activity_id, user_id, COALESCE(f.parent_id, u.faculty_id) AS faculty_id, u.first_name, u.last_name, u.anonymize
            FROM sorted s
            INNER JOIN user u ON u.id = s.user_id
            INNER JOIN faculty f ON u.faculty_id = f.id
            WHERE s.row_num = 1
        ');

        $query->bindValue(1, self::getWeek());
        $query->bindValue(2, true);
        $query->bindValue(3, $season->getId());

        /**
         * @var list<array{first_name: string, last_name: string, anonymize: bool|null, activity_id: int, faculty_id: int, value: string}> $result
         */
        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(
            static fn (array $row): ExtraPointsResultDto => new ExtraPointsResultDto(
                new AnonymizedUser($row['first_name'], $row['last_name'], $row['anonymize']),
                $row['activity_id'],
                $row['faculty_id'],
                (int) $row['value'],
            ),
            $result,
        );
    }

    #[\Override]
    public static function reward(): int
    {
        return 1;
    }
}
