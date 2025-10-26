<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cache;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cache>
 *
 * @method Cache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cache|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Cache[]    findAll()
 * @method Cache[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class SeasonCacheRepository extends ServiceEntityRepository
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
     * @return list<Cache>
     */
    public function findLastN(int $n): array
    {
        /** @var list<Cache> */
        return $this->createQueryBuilder('c')
            ->join('c.season', 's')
            ->orderBy('s.start', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult();
    }

    public function isCached(Season $season): bool
    {
        return (bool) $this->createQueryBuilder('c')
            ->select('CASE WHEN COUNT(c.season) > 0 THEN 1 ELSE 0 END')
            ->where('c.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findBySeason(Season $season): ?Cache
    {
        /** @var Cache|null */
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }
}
