<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Extract\ExtractSubmissionDto;
use App\Dto\OutlierActivity;
use App\Dto\OutlierResult;
use App\Dto\Season\Request\SeasonQueryFilterRequestDto;
use App\Dto\Season\Request\SeasonQueryFilterType;
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
 * @method Submission|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
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
            ->andWhere('s.reviewed = 0')
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
            ->addOrderBy('s.date', 'ASC')
            ->addOrderBy('s.id', 'ASC');

        foreach ($queryFilter->toArray() as $key => $value) {
            $queryBuilder = match ($key) {
                SeasonQueryFilterType::Date->value => $queryBuilder->andWhere(
                    's.date = :date',
                )->setParameter('date', $value),
                SeasonQueryFilterType::Week->value => $queryBuilder->andWhere(
                    's.week = :weekId',
                )->setParameter('weekId', $value),
                SeasonQueryFilterType::Accepted->value
                    => $queryBuilder->andWhere(
                    's.accepted = :accepted',
                )->setParameter('accepted', $value),
                SeasonQueryFilterType::Reviewed->value
                    => $queryBuilder->andWhere(
                    's.reviewed = :reviewed',
                )->setParameter('reviewed', $value),
                SeasonQueryFilterType::User->value => \is_string($value)
                    ? $queryBuilder->andWhere(
                        'u.email LIKE :userId',
                    )->setParameter('userId', $value . '%')
                    : $queryBuilder,
                SeasonQueryFilterType::Faculty->value
                    => $queryBuilder->andWhere(
                    'u.faculty = :facultyId',
                )->setParameter('facultyId', $value),
                SeasonQueryFilterType::Activity->value
                    => $queryBuilder->andWhere(
                    's.activity = :activityId',
                )->setParameter('activityId', $value),
                SeasonQueryFilterType::Page->value => \is_int($value)
                    ? $queryBuilder->setFirstResult(($value - 1) * $limit)
                    : $queryBuilder,
                default => $queryBuilder,
            };
        }

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
            ->andWhere('s.accepted = 1')
            ->groupBy('activity')
            ->addGroupBy('faculty')
            ->orderBy('activity', 'asc')
            ->addOrderBy('distance', 'desc')
            ->setParameter('week', $week)
            ->setParameter('season', $season)
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
                SELECT value, activity_id, user_id, COALESCE(f.parent_id, u.faculty_id) AS faculty_id
                FROM sorted s
                INNER JOIN user u ON u.id = s.user_id
                INNER JOIN faculty f ON u.faculty_id = f.id
                WHERE s.row_num <= ?
                ORDER BY value DESC
            ');

        $query->bindValue(1, true);
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
            ->where('s.accepted = 1')
            ->groupBy('s.season')
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
            ->createQueryBuilder('ss')
            ->select('ss, i')
            ->join('ss.image', 'i')
            ->where('ss.reviewed = 1')
            ->andWhere('ss.image IS NOT NULL')
            ->orderBy('ss.season');

        if ($season !== null) {
            $qb->where('ss.season = (:season)')->setParameter(
                'season',
                $season,
            );
        }

        /** @var list<Submission> */
        $result = $qb->getQuery()->getResult();

        return \array_map(
            static fn(Submission $submission): ExtractSubmissionDto => new ExtractSubmissionDto(
                $submission->activity->id,
                $submission->season->id,
                $submission->accepted,
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
            ->where('s.accepted = 1')
            ->groupBy('s.activity')
            ->getQuery()
            ->getResult();
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
