<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\RejectedSubmissionMessage;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RejectedSubmissionMessage>
 *
 * @method RejectedSubmissionMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method RejectedSubmissionMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method RejectedSubmissionMessage[]    findAll()
 * @method RejectedSubmissionMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RejectedSubmissionMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RejectedSubmissionMessage::class);
    }

    public function save(RejectedSubmissionMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RejectedSubmissionMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUser(User $user): array
    {
        $query = $this->getEntityManager()->getConnection()->prepare('
            SELECT * FROM (
                SELECT null as message, s.id as s_id, s.activity_id, s.week, s.distance, s.elevation, s.accepted, s.reviewed, s.date, s.image
                FROM submission s
                WHERE s.user_id = :user
                UNION ALL
                SELECT m.message, s.id as s_id, s.activity_id, s.week, s.distance, s.elevation, s.accepted, s.reviewed, s.date, s.image
                FROM rejected_submission_message m
                INNER JOIN submission s ON s.id = m.id
                WHERE s.user_id = :user
            ) s
            ORDER BY s.date DESC
        ');

        $query->bindValue('user', $user->getId());

        return $query->executeQuery()->fetchAssociative();
    }
}
