<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Faculty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faculty>
 *
 * @method Faculty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faculty|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method Faculty[]    findAll()
 * @method Faculty[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class FacultyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faculty::class);
    }

    public function save(Faculty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Faculty $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<int, Faculty>
     */
    public function findAllWithTranslations(): array
    {
        /** @var array<int, Faculty> */
        return $this->createQueryBuilder('f')
            ->join('f.translations', 'ft')
            ->addSelect('ft')
            ->indexBy('f', 'f.id')
            ->getQuery()
            ->getResult();
    }
}
