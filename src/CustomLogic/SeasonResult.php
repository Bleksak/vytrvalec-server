<?php

namespace App\CustomLogic;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;

class SeasonResult
{
    public function __construct(private readonly EntityManagerInterface $entityManagerInterface)
    {
    }

    public function calculate(Season $season): array
    {
        $query = $this->entityManagerInterface->getConnection()->prepare('
            SELECT SUM(s.distance) AS distance, u.faculty_id AS faculty_id, s.activity_id AS activity_id
            FROM submission s
            INNER JOIN user u ON s.user_id = u.id
            WHERE s.accepted = ? AND s.season_id = ?
            GROUP BY activity_id, faculty_id
            ORDER BY activity_id ASC, distance DESC;
        ');

        $query->bindValue(1, true);
        $query->bindValue(2, $season->getId());

        return $query->executeQuery()->fetchAllAssociative();
    }
}
