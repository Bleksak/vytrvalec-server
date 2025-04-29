<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploader
{
    public function __construct(
        private readonly Filesystem $fs,
        private readonly ParameterBagInterface $parameterBag,
        private readonly ImageRepository $imageRepository,
    ) {
    }

    public function uploadImage(UploadedFile $image): ?Image
    {
        $dirname = $this->parameterBag->get('kernel.project_dir').'/public';

        do {
            $uniquePath = '/uploads/'.uniqid(more_entropy: true).'.webp';
            $absolutePath = $dirname.$uniquePath;
        } while ($this->fs->exists($absolutePath));

        $tmpPath = $uniquePath.'.tmp';

        $newFile = $image->move($dirname, $tmpPath);

        try {
            $img = new \Imagick($newFile->getRealPath());
            $profiles = $img->getImageProfiles('icc');

            $img->stripImage();
            if (!empty($profiles)) {
                $img->profileImage('icc', $profiles['icc']);
            }

            $img->setImageFormat('webp');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (\ImagickException) {
            return null;
        } finally {
            $this->fs->remove($newFile->getRealPath());
        }

        $image = new Image($uniquePath);

        $this->imageRepository->save($image, true);

        return $image;
    }
}
