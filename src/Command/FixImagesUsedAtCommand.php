<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Charity;
use App\Entity\Image;
use App\Entity\Submission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'mv:fix-images-used-at', description: 'Fixes usedAt on all images')]
final readonly class FixImagesUsedAtCommand
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $now = new \DateTimeImmutable();

        $qb = $this->em->createQueryBuilder();
        $updated = $qb
            ->update(Image::class, 'i')
            ->set('i.usedAt', ':now')
            ->setParameter('now', $now)
            ->where('i.usedAt IS NULL')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->exists(
                    $this->em
                        ->createQueryBuilder()
                        ->select('1')
                        ->from(Charity::class, 'c')
                        ->where('IDENTITY(c.image) = i.uuid')
                        ->getDQL(),
                ),
                $qb->expr()->exists(
                    $this->em
                        ->createQueryBuilder()
                        ->select('1')
                        ->from(Submission::class, 's')
                        ->where('IDENTITY(s.image) = i.uuid')
                        ->getDQL(),
                ),
            ))
            ->getQuery()
            ->getSingleScalarResult();

        $io->success(sprintf('Updated %d images', (int) $updated));

        return Command::SUCCESS;
    }
}
