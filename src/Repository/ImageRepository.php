<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Image>
 */
final class ImageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @return list<Image>
     */
    public function findUnusedImagesForRemoval(): array
    {
        $weekAgo = new \DateTime()->sub(new \DateInterval('P1W'));

        /** @var list<Image> */
        return $this
            ->createQueryBuilder('i')
            ->select('i')
            ->where('i.usedAt IS NULL')
            ->andWhere('i.uploadedAt <= :weekAgo')
            ->setParameter('weekAgo', $weekAgo)
            ->getQuery()
            ->getResult();
    }
}
