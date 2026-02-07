<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FacultyMapping;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacultyMapping>
 */
final class FacultyMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacultyMapping::class);
    }

    public function save(FacultyMapping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FacultyMapping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Returns a hashmap of faculty id => root parent faculty id
     * @return array<int, int>
     * */
    public function findRootsBySeason(Season $season): array
    {
        $queryResult = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery('
                WITH RECURSIVE faculty_tree AS (
                    SELECT
                        f.id AS original_faculty_id,
                        f.id AS current_faculty_id,
                        fm.parent_id
                    FROM faculty f
                    LEFT JOIN faculty_mapping fm
                        ON fm.faculty_id = f.id
                       AND fm.season_id = :season_id

                    UNION ALL

                    SELECT
                        ft.original_faculty_id,
                        p.id AS current_faculty_id,
                        fm.parent_id
                    FROM faculty_tree ft
                    JOIN faculty p
                        ON p.id = ft.parent_id
                    LEFT JOIN faculty_mapping fm
                        ON fm.faculty_id = p.id
                       AND fm.season_id = :season_id
                    WHERE ft.parent_id IS NOT NULL
                )

                SELECT
                    original_faculty_id AS faculty_id,
                    current_faculty_id  AS root_faculty_id
                FROM faculty_tree
                WHERE parent_id IS NULL;
            ', [
                'season_id' => $season->id,
            ]);

        /** @var array<array{faculty_id: int, root_faculty_id: int}> */
        $data = $queryResult->fetchAllAssociative();

        $result = [];

        foreach ($data as $row) {
            $result[$row['faculty_id']] = $row['root_faculty_id'];
        }

        return $result;
    }

    public function removeBySeason(Season $season, bool $flush = false): void
    {
        $this
            ->createQueryBuilder('fm')
            ->delete('fm')
            ->where('fm.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
