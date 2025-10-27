<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Season\SeasonIndexDto;
use App\Entity\Charity;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Season>
 *
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function save(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCurrent(): ?Season
    {
        /** @var Season|null */
        return $this->createQueryBuilder('s')
            ->where('s.start <= :now')
            ->andWhere('s.end >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    public function getLast(): ?Season
    {
        /** @var Season|null */
        return $this->createQueryBuilder('s')
            ->select('s')
            ->orderBy('s.end', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @return list<SeasonIndexDto>
     */
    public function findOrdered(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s', 'c', 'i', 'ct', 'sfm')
            // Add a subquery instead of a join
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
    public function findPast(): array
    {
        /** @var list<Season> */
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.end < :now')
            ->orderBy('s.start', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function findByStartMonth(\DateTime $dateTime): ?Season
    {
        $startDate = new \DateTimeImmutable($dateTime->format('Y-m-01'));
        $endDate = new \DateTimeImmutable($dateTime->format('Y-m-t'));

        /** @var Season|null */
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.start BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    public function countSeasonsByCharity(Charity $charity): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->where('s.charity = :charity')
            ->setParameter('charity', $charity)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
