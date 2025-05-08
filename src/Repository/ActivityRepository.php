<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        return intval($this->createQueryBuilder('a')
            ->select('COUNT(*)')
            ->from(Submission::class, 's')
            ->where(['s.activity = :activity'])
            ->setParameter('activity', $activity->getId())
            ->getQuery()
            ->getSingleScalarResult());
    }
}
