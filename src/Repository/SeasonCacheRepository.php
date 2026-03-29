<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cache;
use App\Entity\Season;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Cache>
 */
final class SeasonCacheRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cache::class);
    }

    /**
     * @return list<Cache>
     */
    public function findLastN(int $n): array
    {
        /** @var list<Cache> */
        return $this
            ->createQueryBuilder('c')
            ->join('c.season', 's')
            ->orderBy('s.start', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult();
    }

    public function isCached(Season $season): bool
    {
        return (bool) $this
            ->createQueryBuilder('c')
            ->select('CASE WHEN COUNT(c.season) > 0 THEN 1 ELSE 0 END')
            ->where('c.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findBySeason(Season $season): ?Cache
    {
        /** @var Cache|null */
        return $this
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }
}
