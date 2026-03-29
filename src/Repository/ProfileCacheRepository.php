<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProfileCache;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ProfileCache>
 *
 * @method ProfileCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileCache|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method ProfileCache[]    findAll()
 * @method ProfileCache[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class ProfileCacheRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileCache::class);
    }

    public function addCache(Submission $submission, bool $flush = false): void
    {
        $profileCache = $this->findOneBy([
            'user' => $submission->user,
            'activity' => $submission->activity,
        ]);

        $profileCache ??= new ProfileCache(
            $submission->user,
            $submission->activity,
        );

        $profileCache->distance += $submission->distance;
        $profileCache->elevation += $submission->elevation;

        $this->save($profileCache, $flush);
    }

    public function removeCache(
        Submission $submission,
        bool $flush = false,
    ): void {
        $profileCache = $this->findOneBy([
            'user' => $submission->user,
            'activity' => $submission->activity,
        ]);

        if ($profileCache !== null) {
            $profileCache->distance -= $submission->distance;
            $profileCache->elevation -= $submission->elevation;

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
            $activity = $cache->activity;

            /**
             * @var SubmissionRepository
             */
            $submissionRepository = $this->getEntityManager()->getRepository(Submission::class);
            $submissions = $submissionRepository->findBy([
                'activity' => $activity,
                'user' => $user,
            ]);

            $elevation = 0;
            $distance = 0;

            foreach ($submissions as $submission) {
                $distance += $submission->distance;
                $elevation += $submission->elevation;
            }

            $cache->distance = $distance;
            $cache->elevation = $elevation;

            $this->save($cache);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return list<ProfileCache>
     */
    public function findAllByUserWithActivities(
        User $user,
        ?string $locale = null,
    ): array {
        $query = $this
            ->createQueryBuilder('pc')
            ->addSelect('pca')
            ->addSelect('pcai')
            ->addSelect('pcat')
            ->andWhere('pc.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('pc.activity', 'pca')
            ->leftJoin('pca.icon', 'pcai');

        if ($locale !== null) {
            $query->innerJoin(
                'pca.translations',
                'pcat',
                Join::WITH,
                'pcat.locale = :locale',
            )->setParameter('locale', $locale);
        } else {
            $query->innerJoin('pca.translations', 'pcat');
        }

        /** @var list<ProfileCache> */
        return $query->getQuery()->getResult();
    }
}
