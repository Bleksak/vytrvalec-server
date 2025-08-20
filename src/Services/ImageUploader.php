<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploader
{
    private string $publicDirectory;

    public function __construct(
        private Filesystem $fs,
        private ImageRepository $imageRepository,
        string $projectDirectory,
    ) {
        $this->publicDirectory = $projectDirectory.'/public';
    }

    public function uploadImage(UploadedFile $image): ?Image
    {
        do {
            $uniquePath = '/uploads/'.uniqid(more_entropy: true).'.webp';
            $absolutePath = $this->publicDirectory.$uniquePath;
        } while ($this->fs->exists($absolutePath));

        $tmpPath = $uniquePath.'.tmp';

        $newFile = $image->move($this->publicDirectory, $tmpPath);

        $filePath = $newFile->getRealPath();

        if ($filePath === false) {
            return null;
        }

        try {
            $img = new \Imagick($filePath);
            $profiles = $img->getImageProfiles('icc');

            $img->stripImage();
            if (count($profiles) !== 0) {
                $img->profileImage('icc', $profiles['icc']);
            }

            $img->setImageFormat('webp');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (\ImagickException) {
            return null;
        } finally {
            $this->fs->remove($filePath);
        }

        $image = new Image($uniquePath);

        $this->imageRepository->save($image, true);

        return $image;
    }
}
