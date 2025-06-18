<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function save(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Image>
     */
    public function findUnusedImagesForRemoval(): array
    {
        $weekAgo = new \DateTime()->sub(new \DateInterval('P1W'));

        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.usedAt IS NULL')
            ->andWhere('i.uploadedAt <= :weekAgo')
            ->setParameter('weekAgo', $weekAgo)
            ->getQuery()
            ->getResult();
    }
}
