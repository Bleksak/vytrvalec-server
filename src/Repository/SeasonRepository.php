<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Season\SeasonIndexDto;
use App\Entity\Charity;
use App\Entity\Season;
use App\Entity\User;
use App\Utils\FeatureFlag;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Season>
 */
final class SeasonRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function findCurrentSeason(?User $user): ?Season
    {
        $currentDate = new \DateTimeImmutable();
        $currentEndDate = $currentDate->modify('+1 day');

        $qb = $this
            ->createQueryBuilder('s')
            ->where('s.start <= :now')
            ->andWhere('s.end >= :end')
            ->setParameter('now', $currentDate)
            ->setParameter('now', $currentEndDate)
            ->setMaxResults(1);

        if ($user === null || !$user->canAccess(FeatureFlag::ROLE_STAFF)) {
            $qb->andWhere('s.isTest = false');
        }

        /** @var Season|null */
        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    public function getLast(): ?Season
    {
        /** @var Season|null */
        return $this
            ->createQueryBuilder('s')
            ->select('s')
            ->orderBy('s.end', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @return list<SeasonIndexDto>
     */
    public function findOrdered(?User $user): array
    {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s', 'c', 'i', 'ct', 'sfm')
            ->addSelect('(
            SELECT COUNT(sub2.id)
            FROM App\Entity\Submission sub2
            WHERE sub2.season = s
        ) AS submissionCount')
            ->join('s.charity', 'c')
            ->leftJoin('s.facultyMappings', 'sfm')
            ->leftJoin('c.image', 'i')
            ->leftJoin('c.translations', 'ct')
            ->orderBy('s.start', 'DESC');

        if ($user === null || !$user->canAccess(FeatureFlag::ROLE_STAFF)) {
            $qb->andWhere('s.isTest = false');
        }

        /** @var list<array{0: Season, submissionCount: int}> */
        $results = $qb->getQuery()->getResult();

        $result = [];

        foreach ($results as $row) {
            $result[] = new SeasonIndexDto(
                $row[0],
                (int) $row['submissionCount'] === 0,
            );
        }

        return $result;
    }

    /**
     * @return list<Season>
     */
    public function findPast(?User $user): array
    {
        $qb = $this
            ->createQueryBuilder('s')
            ->addSelect('s')
            ->addSelect('sc')
            ->addSelect('sci')
            ->addSelect('sct')
            ->innerJoin('s.charity', 'sc')
            ->innerJoin('sc.image', 'sci')
            ->innerJoin('sc.translations', 'sct')
            ->where('s.end < :now')
            ->orderBy('s.start', 'DESC')
            ->setParameter('now', new \DateTimeImmutable());

        if ($user === null || !$user->canAccess(FeatureFlag::ROLE_STAFF)) {
            $qb->andWhere('s.isTest = false');
        }

        /** @var list<Season> */
        return $qb->getQuery()->getResult();
    }

    public function findByStartMonth(\DateTime $dateTime): ?Season
    {
        $startDate = new \DateTimeImmutable($dateTime->format('Y-m-01'));
        $endDate = new \DateTimeImmutable($dateTime->format('Y-m-t'));

        /** @var Season|null */
        return $this
            ->createQueryBuilder('s')
            ->select('s')
            ->where('s.start BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    public function countSeasonsByCharity(Charity $charity): int
    {
        return (int) $this
            ->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->where('s.charity = :charity')
            ->setParameter('charity', $charity)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /** @return array<int, Season> */
    public function findAllVisible(?string $locale = null): array
    {
        $query = $this
            ->createQueryBuilder('s')
            ->addSelect('sc')
            ->addSelect('sct')
            ->indexBy('s', 's.id')
            ->innerJoin('s.charity', 'sc');

        if ($locale !== null) {
            $query->innerJoin(
                'sc.translations',
                'sct',
                Join::WITH,
                'sct.locale = :locale',
            )->setParameter('locale', $locale);
        } else {
            $query->innerJoin('sc.translations', 'sct');
        }

        /** @var array<int, Season> */
        return $query->getQuery()->getResult();
    }
}
