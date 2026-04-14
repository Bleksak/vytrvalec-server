<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\ExtraPointsResultDto;
use App\Entity\Season;
use App\Utils\SubmissionState;
use Doctrine\ORM\EntityManagerInterface;

final readonly class WeeklyElevationExtraPoints implements ExtraPointsInterface
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
    ) {}

    #[\Override]
    public static function getUniqueName(): string
    {
        return 'weekly_elevation';
    }

    #[\Override]
    public static function getWeek(): int
    {
        return 3;
    }

    #[\Override]
    public function calculate(Season $season): array
    {
        $query = $this->entityManagerInterface
            ->getConnection()
            ->prepare('
            WITH
                sub AS (
                    SELECT MAX(s.elevation) as value, a.min_elevation, s.activity_id as activity_id, s.user_id as user_id
                    FROM submission s
                    INNER JOIN activity a ON s.activity_id = a.id
                    WHERE s.week = ? AND s.state = ? AND s.season_id = ?
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
            SELECT value, activity_id, user_id, COALESCE(f.parent_id, u.faculty_id) AS faculty_id, u.first_name, u.last_name, u.anonymize
            FROM sorted s
            INNER JOIN user u ON u.id = s.user_id
            INNER JOIN faculty f ON u.faculty_id = f.id
            WHERE s.row_num = 1
        ');

        $query->bindValue(1, self::getWeek());
        $query->bindValue(2, SubmissionState::Accepted->value);
        $query->bindValue(3, $season->id);

        /**
         * @var list<array{user_id: int, activity_id: int, faculty_id: int, value: string}> $result
         */
        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(
            /** @param array{user_id: int, activity_id: int, faculty_id: int, value: string} $row */
            static fn(array $row): ExtraPointsResultDto => new ExtraPointsResultDto(
                $row['user_id'],
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
