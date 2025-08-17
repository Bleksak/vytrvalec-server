<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ImageRemoverSchedule
{
    public function __construct(
        private ImageRepository $imageRepository,
        private EntityManagerInterface $em,
        private Filesystem $fs,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ImageRemoverMessage $_message): void
    {
        $this->logger->info('ImageRemoverSchedule was called');
        $images = $this->imageRepository->findUnusedImagesForRemoval();

        $imageCount = count($images);

        foreach ($images as $image) {
            $this->fs->remove($image->getPath());
            $this->imageRepository->remove($image);
        }

        $this->logger->info(sprintf('Removed %d images', $imageCount));

        $this->em->flush();
    }
}
