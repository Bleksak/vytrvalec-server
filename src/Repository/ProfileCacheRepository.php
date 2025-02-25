<?php

namespace App\Repository;

use App\Entity\ProfileCache;
use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfileCache>
 *
 * @method ProfileCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileCache[]    findAll()
 * @method ProfileCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileCache::class);
    }

    public function save(ProfileCache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addCache(Submission $submission, bool $flush = false): void
    {
        $profileCache = $this->findOneBy(['user' => $submission->getUser(), 'activity' => $submission->getActivity()]);
        $profileCache = $profileCache ?? new ProfileCache($submission->getUser(), $submission->getActivity());

        $profileCache
            ->updateDistance(fn (int $oldDistance) => $oldDistance + $submission->getDistance())
            ->updateElevation(fn (int $oldElevation) => $oldElevation + $submission->getElevation())
        ;

        $this->save($profileCache, $flush);
    }

    public function removeCache(Submission $submission, bool $flush): void
    {
        $profileCache = $this->findOneBy(['user' => $submission->getUser(), 'activity' => $submission->getActivity()]);

        if ($profileCache !== null) {
            $profileCache
                ->updateDistance(fn (int $oldDistance) => $oldDistance - $submission->getDistance())
                ->updateElevation(fn (int $oldElevation) => $oldElevation - $submission->getElevation())
            ;

            $this->save($profileCache, $flush);
        }
    }
}
