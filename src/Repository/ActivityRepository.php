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
 * @method Activity[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
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

        return intval(
            $qb
                ->select($qb->expr()->count('a'))
                ->from(Submission::class, 's')
                ->where(['s.activity = :activity'])
                ->setParameter('activity', $activity->getId())
                ->getQuery()
                ->getSingleScalarResult(),
        );
    }

    /**
     * @return array<ActivityStatisticsDto>
     */
    public function getTotalStatistics(?ImagePath $imagePath = null): array
    {
        /**
         * @var array<array{0: Activity, distance: string}> $data
         */
        $data = $this->createQueryBuilder('a')
            ->addSelect('a as activity')
            ->addSelect('SUM(s.distance) as distance')
            ->join(Submission::class, 's', Join::WITH, 's.activity = a')
            ->where('s.accepted = 1')
            ->groupBy('s.activity')
            ->getQuery()
            ->getResult();

        return array_map(
            static fn (array $row): ActivityStatisticsDto => new ActivityStatisticsDto(
                $row[0]->toResponseObject($imagePath),
                (int) $row['distance'],
            ),
            $data,
        );
    }
}
