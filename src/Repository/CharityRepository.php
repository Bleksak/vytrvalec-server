<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Charity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Charity>
 */
final class CharityRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charity::class);
    }
}
