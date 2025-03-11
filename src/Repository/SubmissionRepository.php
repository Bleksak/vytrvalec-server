<?php

namespace App\Repository;

use App\Dto\ActivityStatisticsDto;
use App\Dto\AnonymizedUserCandidate;
use App\Dto\OutlierActivity;
use App\Dto\OutlierResult;
use App\Dto\WeeklySubmissionSum;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 *
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    public function save(Submission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Submission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Paginator<Submission>
     */
    public function findAllByUser(User $user, int $page, int $limit): Paginator
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->where('s.user = :userId')
            ->addOrderBy('s.date', 'DESC')
            ->setParameter('userId', $user->getId());

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @return Paginator<Submission>
     */
    public function findBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->where('s.season = :seasonId')
            ->orderBy('s.date', 'DESC')
            ->setParameter('seasonId', $season->getId());

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @return array<int,Submission>
     */
    public function findUnreviewed(int $limit): array
    {
        return $this
            ->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.reviewed = :reviewed')
            ->setParameter('reviewed', false)
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<ActivityStatisticsDto>
     */
    public function getTotalStatistics(): array
    {
        $query = $this->getEntityManager()
            ->getConnection()
            ->prepare('
            SELECT a.name as activity, sub.distance as distance
            FROM (
                SELECT s.activity_id as activity_id, SUM(s.distance) as distance
                FROM submission s
                WHERE s.accepted = 1
                GROUP BY s.activity_id
            ) sub
            INNER JOIN activity a ON a.id = sub.activity_id;
        ');

        $data = $query->executeQuery()->fetchAllAssociative();

        return array_map(
            static fn ($row) => new ActivityStatisticsDto($row['activity'], $row['distance']),
            $data
        );
    }

    /**
     * @param array<string,string> $filter
     *
     * @return Paginator<Submission>
     */
    public function findBySeasonAndFilter(Season $season, array $filter, int $page, int $limit): Paginator
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->where('s.season = :seasonId')
            ->join('s.user', 'u')
            ->setParameter('seasonId', $season->getId())
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->addOrderBy('s.date', 'ASC')
            ->addOrderBy('s.id', 'ASC');

        foreach ($filter as $key => $value) {
            $queryBuilder = match ($key) {
                'date' => $queryBuilder
                    ->andWhere('s.date = :date')
                    ->setParameter('date', $value),

                'week' => $queryBuilder
                    ->andWhere('s.week = :weekId')
                    ->setParameter('weekId', $value),

                'accepted' => $queryBuilder
                    ->andWhere('s.accepted = :accepted')
                    ->setParameter('accepted', $value),

                'reviewed' => $queryBuilder
                    ->andWhere('s.reviewed = :reviewed')
                    ->setParameter('reviewed', $value),

                'user' => $queryBuilder
                    ->andWhere('u.email LIKE :userId')
                    ->setParameter('userId', $value.'%'),

                'faculty' => $queryBuilder
                    ->andWhere('u.faculty = :facultyId')
                    ->setParameter('facultyId', $value),

                'activity' => $queryBuilder
                    ->andWhere('s.activity = :activityId')
                    ->setParameter('activityId', $value),

                default => $queryBuilder,
            };
        }

        $paginator = new Paginator($queryBuilder->getQuery());

        return $paginator;
    }

    /**
     * @return array<WeeklySubmissionSum>
     */
    public function getResultsForWeek(Season $season, int $week): array
    {
        $result = $this->createQueryBuilder('s')
            ->select('sum(s.distance) as distance, COALESCE(IDENTITY(f.parent), IDENTITY(u.faculty)) as faculty, IDENTITY(s.activity) as activity')
            ->innerJoin('s.user', 'u')
            ->innerJoin('u.faculty', 'f')
            ->where('s.week = :week')
            ->andWhere('s.accepted = 1')
            ->andWhere('s.season = :season')
            ->groupBy('activity')
            ->addGroupBy('faculty')
            ->orderBy('activity', 'asc')
            ->addOrderBy('distance', 'desc')
            ->setParameter('week', $week)
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        return array_map(
            fn ($row) => new WeeklySubmissionSum($row['distance'], $row['faculty'], $row['activity']),
            $result,
        );
    }

    /**
     * @return array<OutlierActivity>
     */
    public function findOutliers(Season $season, int $n = 3): array
    {
        $query = $this->getEntityManager()->getConnection()->prepare('
            WITH
                sub AS (
                    SELECT MAX(s.distance) as value, s.activity_id as activity_id, s.user_id as user_id
                    FROM submission s
                    INNER JOIN activity a ON s.activity_id = a.id
                    WHERE s.accepted = ? AND s.season_id = ?
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
            WHERE s.row_num <= 3
            ORDER BY value DESC
        ');

        $query->bindValue(1, true);
        $query->bindValue(2, $season->getId());

        $result = $query->executeQuery()->fetchAllAssociative();

        $activities = [];

        foreach ($result as $row) {
            if (!array_key_exists($row['activity_id'], $activities)) {
                $activities[$row['activity_id']] = [];
            }

            $activities[$row['activity_id']][] = new OutlierResult(
                (new AnonymizedUserCandidate(
                    $row['first_name'],
                    $row['last_name'],
                    $row['accepted_gdpr'],
                ))->anonymize(),
                $row['faculty_id'],
                $row['value'],
            );
        }

        $outlierActivity = [];

        foreach ($activities as $id => $results) {
            $outlierActivity[] = new OutlierActivity(
                $id,
                $results
            );
        }

        return $outlierActivity;
    }
}
