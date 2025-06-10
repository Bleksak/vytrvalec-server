<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\ActivityStatisticsDto;
use App\Dto\AnonymizedUser;
use App\Dto\Extract\ExtractSubmissionDto;
use App\Dto\OutlierActivity;
use App\Dto\OutlierResult;
use App\Dto\WeeklySubmissionSum;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Services\ImagePath;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 *
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
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
    public function findAllByUser(
        User $user,
        int $page,
        int $limit,
    ): Paginator {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->join('s.image', 'i')
            ->where('s.user = :userId')
            ->addOrderBy('s.date', 'DESC')
            ->setParameter('userId', $user->getId());

        /**
         * @var Paginator<Submission>
         */
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
            ->join('s.image', 'i')
            ->addSelect('i.path as image')
            ->where('s.season = :seasonId')
            ->orderBy('s.date', 'DESC')
            ->setParameter('seasonId', $season->getId());

        /**
         * @var Paginator<Submission>
         */
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
        /**
         * @var array<Submission>
         */
        $result = $this
            ->createQueryBuilder('s')
            ->select('s, i, u')
            ->join('s.image', 'i')
            ->join('s.user', 'u')
            ->andWhere('s.reviewed = 0')
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $indexed = [];

        foreach ($result as $row) {
            $indexed[$row->getId()] = $row;
        }

        return $indexed;
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

        /**
         * @var array<array{activity: string, distance: string}> $data
         */
        $data = $query->executeQuery()->fetchAllAssociative();

        return array_map(
            static fn (array $row): ActivityStatisticsDto => new ActivityStatisticsDto(
                $row['activity'],
                (int) $row['distance']
            ),
            $data
        );
    }

    /**
     * @param array<string,string|int> $filter
     *
     * @return Paginator<Submission>
     */
    public function findBySeasonAndFilter(Season $season, array $filter, int $page, int $limit): Paginator
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->join('s.image', 'i')
            // ->addSelect('coalesce(i.path, \'\') as image')
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

        /**
         * @var Paginator<Submission>
         */
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
            fn (array $row): WeeklySubmissionSum => new WeeklySubmissionSum((int) $row['distance'], $row['faculty'], $row['activity']),
            $result,
        );
    }

    /**
     * @return array<OutlierActivity>
     */
    public function findOutliers(Season $season, int $n = 3, bool $shouldAnonymize = true): array
    {
        $query = $this->getEntityManager()->getConnection()->prepare('
            WITH
                sub AS (
                    SELECT SUM(s.distance) as value, s.activity_id as activity_id, s.user_id as user_id
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
            SELECT value, activity_id, user_id, COALESCE(f.parent_id, u.faculty_id) AS faculty_id, u.first_name, u.last_name, u.anonymize
            FROM sorted s
            INNER JOIN user u ON u.id = s.user_id
            INNER JOIN faculty f ON u.faculty_id = f.id
            WHERE s.row_num <= 3
            ORDER BY value DESC
        ');

        $query->bindValue(1, true);
        $query->bindValue(2, $season->getId());

        /**
         * @var array<array{activity_id: int, anonymize: bool, first_name: string, last_name: string, faculty_id: int, value: string}> $result
         */
        $result = $query->executeQuery()->fetchAllAssociative();

        $activities = [];

        foreach ($result as $row) {
            if (!array_key_exists($row['activity_id'], $activities)) {
                $activities[$row['activity_id']] = [];
            }

            $anonymize = $shouldAnonymize ? $row['anonymize'] : false;

            $activities[$row['activity_id']][] = new OutlierResult(
                new AnonymizedUser(
                    $row['first_name'],
                    $row['last_name'],
                    $anonymize,
                ),
                $row['faculty_id'],
                (int) $row['value'],
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

    public function sumCountUserGroupedByFaculties(): int
    {
        $innerQuery = $this->createQueryBuilder('s')
            ->select('count(distinct s.user) as count')
            ->where('s.accepted = 1')
            ->groupBy('s.season')
            ->getQuery()
            ->getScalarResult();

        return array_sum(array_column($innerQuery, 'count'));
    }

    /**
     * @param array<int>|null $seasons
     *
     * @return array<ExtractSubmissionDto>
     */
    public function extractBySeasons(
        ImagePath $imagePath,
        ?array $seasons,
    ): array {
        $qb = $this->createQueryBuilder('ss')
            ->select('ss.accepted, identity(ss.season) as season_id, identity(ss.activity) as activity_id, ss.distance, ss.elevation, i.image as image')
            ->join('ss.image', 'i')
            ->where('ss.reviewed = 1')
            ->andWhere('ss.image != \'\'')
            ->orderBy('ss.season');

        if ($seasons !== null) {
            $qb->where('ss.season IN (:seasons)')
                ->setParameter('seasons', $seasons);
        }

        $result = $qb->getQuery()->getResult();

        return array_map(
            static fn (array $row): ExtractSubmissionDto => new ExtractSubmissionDto(
                $row['activity_id'],
                $row['season_id'],
                $row['accepted'],
                (int) $row['distance'],
                (int) $row['elevation'],
                $imagePath->fullPath($row['image']),
            ),
            $result
        );
    }
}
