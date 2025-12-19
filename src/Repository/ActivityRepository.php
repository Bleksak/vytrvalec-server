<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\ActivityStatisticsDto;
use App\Entity\Activity;
use App\Entity\Submission;
use App\Services\ImagePath;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Activity::class);
    }

    public function save(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function submissionsCount(Activity $activity): int
    {
        $qb = $this->createQueryBuilder('a');

        return \intval(
            $qb
                ->select($qb->expr()->count('a'))
                ->from(Submission::class, 's')
                ->where(['s.activity = :activity'])
                ->setParameter('activity', $activity->id)
                ->getQuery()
                ->getSingleScalarResult(),
        );
    }

    /**
     * @return list<ActivityStatisticsDto>
     */
    public function getTotalStatistics(
        ?ImagePath $imagePath = null,
        ?string $locale = null,
    ): array {
        $activityListWithDistances =
            $this->submissionRepository->getTotalStatistics();

        $indexMap = [];

        foreach ($activityListWithDistances as $activityWithDistance) {
            $activityId = $activityWithDistance['activity'];
            $distance = $activityWithDistance['distance'];

            $indexMap[$activityId] = (int) $distance;
        }

        $activities = \array_map(
            static fn(array $row): int => $row['activity'],
            $activityListWithDistances,
        );

        $query = $this
            ->createQueryBuilder('a')
            ->addSelect('a as activity')
            ->addSelect('at')
            ->addSelect('ai')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $activities)
            ->innerJoin('a.icon', 'ai')
            ->addGroupBy('a.id');

        if ($locale !== null) {
            $query->innerJoin(
                'a.translations',
                'at',
                Join::WITH,
                'at.locale = :locale',
            )->setParameter('locale', $locale);
        } else {
            $query->innerJoin('a.translations', 'at');
        }

        /**
         * @var list<Activity>
         */
        $activities = $query->getQuery()->getResult();

        return \array_map(
            static fn(Activity $row): ActivityStatisticsDto => new ActivityStatisticsDto(
                $row->toResponseObject($imagePath),
                $indexMap[$row->id],
            ),
            $activities,
        );
    }

    /**
     * @return array<int, Activity>
     */
    public function findAllWithTranslations(): array
    {
        /** @var array<int, Activity> */
        return $this
            ->createQueryBuilder('a')
            ->join('a.translations', 'at')
            ->addSelect('at')
            ->indexBy('a', 'a.id')
            ->getQuery()
            ->getResult();
    }
}
