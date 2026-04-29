<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Extract\ExtractSubmissionDto;
use App\Dto\OutlierActivity;
use App\Dto\OutlierResult;
use App\Dto\Season\Request\SeasonQueryFilterRequestDto;
use App\Dto\WeeklySubmissionSum;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Services\ImagePath;
use App\Utils\SubmissionState;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Submission>
 */
final class SubmissionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    /**
     * @return list<Submission>
     */
    public function findAllByUser(User $user): array
    {
        /** @var list<Submission> */
        return $this
            ->createQueryBuilder('s')
            ->select('s')
            ->addSelect('i')
            ->indexBy('s', 's.id')
            ->leftJoin('s.image', 'i')
            ->where('s.user = :user')
            ->addOrderBy('s.date', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Paginator<Submission>
     */
    public function findBySeason(
        Season $season,
        int $page,
        int $limit,
    ): Paginator {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->join('s.image', 'i')
            ->addSelect('i.path as image')
            ->where('s.season = :seasonId')
            ->orderBy('s.date', 'DESC')
            ->setParameter('seasonId', $season->id);

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
     * @param array<int> $ignoredIds
     *
     * @return array<int,Submission>
     */
    public function findUnreviewed(int $limit, array $ignoredIds = []): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->select('s, i, u')
            ->join('s.image', 'i')
            ->join('s.user', 'u')
            ->andWhere('s.state = :state')
            ->setParameter('state', SubmissionState::Pending)
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit);

        if ($ignoredIds !== []) {
            $qb->andWhere($qb->expr()->notIn(
                's.id',
                ':ignoredIds',
            ))->setParameter('ignoredIds', $ignoredIds);
        }

        /** @var list<Submission> */
        $result = $qb->getQuery()->getResult();

        $indexed = [];

        foreach ($result as $row) {
            $indexed[$row->id] = $row;
        }

        return $indexed;
    }

    /**
     * @return Paginator<Submission>
     */
    public function findBySeasonAndFilter(
        Season $season,
        SeasonQueryFilterRequestDto $queryFilter,
        int $limit,
    ): Paginator {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s', 'u', 'f', 'i')
            ->join('s.image', 'i')
            ->where('s.season = :seasonId')
            ->join('s.user', 'u')
            ->join('u.faculty', 'f')
            ->setParameter('seasonId', $season->id)
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->addOrderBy('s.date', 'DESC')
            ->addOrderBy('s.id', 'DESC');

        if ($queryFilter->page !== null) {
            $queryBuilder->setFirstResult(($queryFilter->page - 1) * $limit);
        }

        $queryBuilder->addCriteria($queryFilter->toCriteria());

        /**
         * @var Paginator<Submission>
         */
        return new Paginator($queryBuilder->getQuery());
    }

    /**
     * @return list<WeeklySubmissionSum>
     */
    public function getResultsForWeek(Season $season, int $week): array
    {
        /** @var list<array{distance: int, faculty: int, activity: int}> */
        $result = $this
            ->createQueryBuilder('s')
            ->select(
                'sum(s.distance) as distance, COALESCE(IDENTITY(f.parent), IDENTITY(u.faculty)) as faculty, IDENTITY(s.activity) as activity',
            )
            ->innerJoin('s.user', 'u')
            ->innerJoin('u.faculty', 'f')
            ->andWhere('s.season = :season')
            ->andWhere('s.week = :week')
            ->andWhere('s.state = :state')
            ->groupBy('activity')
            ->addGroupBy('faculty')
            ->orderBy('activity', 'asc')
            ->addOrderBy('distance', 'desc')
            ->setParameter('week', $week)
            ->setParameter('season', $season)
            ->setParameter('state', SubmissionState::Accepted)
            ->getQuery()
            ->getResult();

        return \array_map(
            /** @param array{distance: int, faculty: int, activity: int} $row */
            static fn(array $row): WeeklySubmissionSum => new WeeklySubmissionSum(
                (int) $row['distance'],
                $row['faculty'],
                $row['activity'],
            ),
            $result,
        );
    }

    /**
     * @return array<int, OutlierActivity>
     */
    public function findOutliers(Season $season, int $n = 3): array
    {
        $query = $this
            ->getEntityManager()
            ->getConnection()
            ->prepare('
                WITH
                    sub AS (
                        SELECT SUM(s.distance) as value, s.activity_id as activity_id, s.user_id as user_id
                        FROM submission s
                        WHERE s.state = ? AND s.season_id = ?
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
                SELECT value, activity_id, user_id, u.faculty_id AS faculty_id
                FROM sorted s
                INNER JOIN user u ON u.id = s.user_id
                WHERE s.row_num <= ?
                ORDER BY value DESC
            ');

        $query->bindValue(1, SubmissionState::Accepted->value);
        $query->bindValue(2, $season->id);
        $query->bindValue(3, $n);

        /**
         * @var list<array{activity_id: int, user_id: int, faculty_id: int, value: string}> $result
         */
        $result = $query->executeQuery()->fetchAllAssociative();

        $activities = [];

        foreach ($result as $row) {
            if (!\array_key_exists($row['activity_id'], $activities)) {
                $activities[$row['activity_id']] = [];
            }

            $activities[$row['activity_id']][] = new OutlierResult(
                $row['user_id'],
                $row['faculty_id'],
                (int) $row['value'],
            );
        }

        $outlierActivity = [];

        foreach ($activities as $id => $results) {
            $outlierActivity[$id] = new OutlierActivity($id, $results);
        }

        return $outlierActivity;
    }

    public function sumCountUserGroupedByFaculties(): int
    {
        /** @var list<int> */
        $result = $this
            ->createQueryBuilder('s')
            ->select('count(distinct s.user) as count')
            ->where('s.state = :state')
            ->groupBy('s.season')
            ->setParameter('state', SubmissionState::Accepted)
            ->getQuery()
            ->getSingleColumnResult();

        return \array_sum($result);
    }

    /**
     * @return list<ExtractSubmissionDto>
     */
    public function extractBySeasons(
        ImagePath $imagePath,
        ?int $season = null,
    ): array {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s, i')
            ->join('s.image', 'i')
            ->where('s.state IN(:state)')
            ->setParameter('state', SubmissionState::reviewedStates())
            ->andWhere('s.image IS NOT NULL')
            ->orderBy('s.season');

        if ($season !== null) {
            $qb->where('s.season = (:season)')->setParameter('season', $season);
        }

        /** @var list<Submission> */
        $result = $qb->getQuery()->getResult();

        return \array_map(
            static fn(Submission $submission): ExtractSubmissionDto => new ExtractSubmissionDto(
                $submission->activity->id,
                $submission->season->id,
                $submission->state,
                $submission->distance,
                $submission->elevation,
                $submission->image?->getPath($imagePath) ?? '',
            ),
            $result,
        );
    }

    /**
     * @return list<array{activity: int, distance: int}>
     */
    public function getTotalStatistics(): array
    {
        /** @var list<array{activity: int, distance: int}> */
        return $this
            ->createQueryBuilder('s')
            ->select(
                'IDENTITY(s.activity) as activity, SUM(s.distance) AS distance',
            )
            ->where('s.state = :state')
            ->groupBy('s.activity')
            ->setParameter('state', SubmissionState::Accepted)
            ->getQuery()
            ->getResult();
    }

    public function hasSubmissions(Season $season): bool
    {
        return $this
            ->createQueryBuilder('s')
            ->select('s.id')
            ->where('s.season = :season')
            ->setParameter('season', $season)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function removeBySeason(Season $season, bool $flush = false): void
    {
        $this
            ->createQueryBuilder('s')
            ->delete()
            ->where('s.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
