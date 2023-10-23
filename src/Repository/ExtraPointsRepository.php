<?php

namespace App\Repository;

use App\Entity\ExtraPoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExtraPoints>
 *
 * @method ExtraPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraPoints|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtraPoints[]    findAll()
 * @method ExtraPoints[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtraPointsRepository extends ServiceEntityRepository
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
