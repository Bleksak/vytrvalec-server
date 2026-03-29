<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Faculty;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Faculty>
 */
final class FacultyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faculty::class);
    }

    /**
     * @return array<int, Faculty>
     */
    public function findAllWithTranslations(?string $locale = null): array
    {
        $query = $this
            ->createQueryBuilder('f')
            ->addSelect('ft')
            ->indexBy('f', 'f.id');

        if ($locale !== null) {
            $query->innerJoin(
                'f.translations',
                'ft',
                Join::WITH,
                'ft.locale = :locale',
            )->setParameter('locale', $locale);
        } else {
            $query->innerJoin('f.translations', 'ft');
        }

        /** @var array<int, Faculty> */
        return $query->getQuery()->getResult();
    }
}
