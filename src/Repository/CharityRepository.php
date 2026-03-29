<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Charity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Charity>
 *
 * @method Charity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charity|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method Charity[]    findAll()
 * @method Charity[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class CharityRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charity::class);
    }
}
