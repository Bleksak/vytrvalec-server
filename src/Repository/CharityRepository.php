<?php

namespace App\Repository;

use App\Entity\Charity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Charity>
 *
 * @method Charity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Charity[]    findAll()
 * @method Charity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CharityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charity::class);
    }

    public function save(Charity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Charity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Charity[]
     */
    public function findSeasonsByCharity(Charity $charity): array
    {
        return $this->createQueryBuilder('c')
            ->select('s')
            ->from('App\Entity\Season', 's')
            ->where('s.charity = :charity')
            ->setParameter('charity', $charity)
            ->getQuery()
            ->getResult();
    }
}
