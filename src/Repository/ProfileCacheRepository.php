<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProfileCache;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfileCache>
 *
 * @method ProfileCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileCache|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method ProfileCache[]    findAll()
 * @method ProfileCache[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class ProfileCacheRepository extends ServiceEntityRepository
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
        $profileCache ??= new ProfileCache($submission->getUser(), $submission->getActivity());

        $newDistance = $profileCache->getDistance() + $submission->getDistance();
        $newElevation = $profileCache->getElevation() + $submission->getElevation();

        $profileCache->setDistance($newDistance)->setElevation($newElevation);

        $this->save($profileCache, $flush);
    }

    public function removeCache(Submission $submission, bool $flush = false): void
    {
        $profileCache = $this->findOneBy(['user' => $submission->getUser(), 'activity' => $submission->getActivity()]);

        if ($profileCache !== null) {
            $newDistance = $profileCache->getDistance() - $submission->getDistance();
            $newElevation = $profileCache->getElevation() - $submission->getElevation();

            $profileCache->setDistance($newDistance)->setElevation($newElevation);

            $this->save($profileCache, $flush);
        }
    }

    // WARNING: This is used only to fix invalid entries, avoid using this in production code
    public function fixCacheValues(User $user): void
    {
        $cachesByUser = $this->findBy(['user' => $user]);

        if (\count($cachesByUser) === 0) {
            return;
        }

        foreach ($cachesByUser as $cache) {
            $activity = $cache->getActivity();

            /**
             * @var SubmissionRepository
             */
            $submissionRepository = $this->getEntityManager()->getRepository(Submission::class);
            $submissions = $submissionRepository->findBy(['activity' => $activity, 'user' => $user]);

            $elevation = 0;
            $distance = 0;

            foreach ($submissions as $submission) {
                $distance += $submission->getDistance();
                $elevation += $submission->getElevation();
            }

            $cache->setDistance($distance);
            $cache->setElevation($elevation);

            $this->save($cache);
        }

        $this->getEntityManager()->flush();
    }
}
