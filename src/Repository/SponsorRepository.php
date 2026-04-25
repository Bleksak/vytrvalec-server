<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Sponsor;
use Doctrine\Persistence\ManagerRegistry;

/** @extends AbstractRepository<Sponsor> */
final class SponsorRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsor::class);
    }

    public function findOneWithSeasons(int $id): ?Sponsor
    {
        /** @var null|Sponsor */
        return $this
            ->createQueryBuilder('s')
            ->addSelect('s')
            ->addSelect('seasons')
            ->leftJoin('s.seasons', 'seasons')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
