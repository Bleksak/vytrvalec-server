<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExtraPoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExtraPoints>
 *
 * @method ExtraPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraPoints|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method ExtraPoints[]    findAll()
 * @method ExtraPoints[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class ExtraPointsRepository extends ServiceEntityRepository
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
