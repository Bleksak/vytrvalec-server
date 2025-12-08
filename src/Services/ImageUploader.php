<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Utils\MimeType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploader
{
    private string $publicDirectory;
    private string $uploadDirectory;

    public function __construct(
        private Filesystem $fs,
        private ImageRepository $imageRepository,
        string $projectDirectory,
    ) {
        $this->publicDirectory = $projectDirectory . '/public';
        $this->uploadDirectory = $this->publicDirectory . '/uploads';
    }

    private function uploadSvg(UploadedFile $image, string $uniquePath): ?string
    {
        $newFile = $image->move($this->uploadDirectory, $uniquePath);

        $filePath = $newFile->getRealPath();
        if ($filePath === false) {
            return null;
        }

        return $uniquePath;
    }

    private function uploadBasicImage(
        UploadedFile $image,
        string $uniquePath,
        string $absolutePath,
    ): ?string {
        $tmpPath = \sprintf('%s.tmp', $uniquePath);
        $newFile = $image->move($this->uploadDirectory, $tmpPath);
        $filePath = $newFile->getRealPath();

        if ($filePath === false) {
            return null;
        }

        try {
            $img = new \Imagick($filePath);
            $profiles = $img->getImageProfiles('icc');

            $img->stripImage();
            if (\count($profiles) !== 0) {
                /** @var string */
                $icc = $profiles['icc'] ?? '';

                $img->profileImage('icc', $icc);
            }

            $img->setImageFormat('webp');
            $img->setImageCompressionQuality(90);
            $img->writeImage($absolutePath);
        } catch (\ImagickException) {
            return null;
        } finally {
            $this->fs->remove($filePath);
        }

        return $uniquePath;
    }

    /**
     * @param array<MimeType> $allowedMimeTypes
     */
    public function uploadImage(
        UploadedFile $image,
        array $allowedMimeTypes,
    ): ?Image {
        $imageMimeType = $image->getMimeType() ?? '';
        $mimeType = MimeType::tryFrom($imageMimeType);

        if (
            $mimeType === null
            || !\in_array($mimeType, $allowedMimeTypes, true)
        ) {
            return null;
        }

        do {
            $uniquePath = \uniqid(more_entropy: true);
            $absolutePath = \sprintf(
                '%s/%s',
                $this->uploadDirectory,
                $uniquePath,
            );
        } while ($this->fs->exists($absolutePath));

        $filePath = match ($mimeType) {
            MimeType::SVG => $this->uploadSvg($image, \sprintf(
                '%s.%s',
                $uniquePath,
                'svg',
            )),
            default => $this->uploadBasicImage(
                $image,
                \sprintf('%s.%s', $uniquePath, 'webp'),
                \sprintf('%s.%s', $absolutePath, 'webp'),
            ),
        };

        if ($filePath === null) {
            return null;
        }

        $image = new Image(\sprintf('/uploads/%s', $filePath), $mimeType);
        $this->imageRepository->save($image, true);

        return $image;
    }
}
