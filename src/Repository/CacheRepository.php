<?php

namespace App\Repository;

use App\Entity\Cache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cache>
 *
 * @method Cache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cache|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cache[]    findAll()
 * @method Cache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cache::class);
    }

    public function save(Cache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, array<int, WeeklyResultDto>>
     */
    public function findLastN(int $n): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.season', 's')
            ->orderBy('s.start', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult();
    }
}
