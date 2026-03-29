<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExtraPoints;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ExtraPoints>
 */
final class ExtraPointsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExtraPoints::class);
    }

    public function findByName(string $name): ?ExtraPoints
    {
        return $this->findOneBy(['name' => $name]);
    }
}
