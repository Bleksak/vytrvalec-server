<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AbstractEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of AbstractEntity
 * @extends ServiceEntityRepository<T>
 *
 * @method T|null find($id, $lockMode = null, $lockVersion = null)
 * @method T|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method list<T>    findAll()
 * @method T[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 * @internal
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param T $entity
     */
    public function save(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param T $entity
     */
    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
