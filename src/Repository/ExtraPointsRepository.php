<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExtraPoints;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ExtraPoints>
 *
 * @method ExtraPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraPoints|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method ExtraPoints[]    findAll()
 * @method ExtraPoints[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
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
