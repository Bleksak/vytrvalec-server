<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Submission\SubmissionFeatures;
use App\Entity\Image;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final readonly class SubmissionImageValidityCheckerService
{
    public function __construct(
        private string $publicDirectory,
        private string $tempDirectory,
    ) {}

    public function readSubmissionImageData(Image $image): ?SubmissionFeatures
    {
        $imagePath = \sprintf(
            '%s/%s',
            $this->publicDirectory,
            $image->getPath(),
        );

        try {
            $imagickImage = new \Imagick($imagePath);
            $imagickImage->setImageColorspace(\Imagick::COLORSPACE_GRAY);
            $imagickImage->contrastImage(true);

            $absolutePath = \sprintf(
                '%s/%s',
                $this->tempDirectory,
                'clean.png',
            );
            $imagickImage->writeImage($absolutePath);
        } catch (\ImagickException) {
            return null;
        }

        $ocrContents =
            SubmissionFeatureReaderOCR::readSubmissionImage($absolutePath);

        if ($ocrContents === null) {
            return null;
        }

        $lines = \explode(\PHP_EOL, $ocrContents);

        $masterFeatures = new SubmissionFeatures(null, null, null);

        $dateCounter = 0;
        $distanceCounter = 0;
        $elevationCounter = 0;

        foreach ($lines as $line) {
            $features = self::extractLineFeatures($line);

            if ($features->date) {
                $dateCounter++;
            }

            if ($features->distance) {
                $distanceCounter++;
            }

            if ($features->elevation) {
                $elevationCounter++;
            }

            $masterFeatures->date ??= $features->date;
            $masterFeatures->distance ??= $features->distance;
            $masterFeatures->elevation ??= $features->elevation;
        }

        if ($distanceCounter > 1) {
            $masterFeatures->distance = null;
        }

        if ($elevationCounter > 1) {
            $masterFeatures->elevation = null;
        }

        if ($dateCounter > 1) {
            $masterFeatures->date = null;
        }

        if (
            $distanceCounter === $elevationCounter
            && $distanceCounter === $dateCounter
            && $distanceCounter === 0
        ) {
            return null;
        }

        return $masterFeatures;
    }

    private static function extractLineFeatures(string $line): SubmissionFeatures
    {
        $line = \preg_replace('/\,/', '.', $line);
        \assert(\is_string($line), 'Line must be a string');
        $line = \preg_replace('/(?<=\d)\s+(?=\d)/', '', $line);
        \assert(\is_string($line), 'Line must be a string');
        $line = \preg_replace('/(?<=\d)\s+(?=\.\d)/', '', $line);
        \assert(\is_string($line), 'Line must be a string');
        $line = \trim($line);

        $distance = null;
        $elevation = null;
        $date = null;

        $kmMatches = [];
        if (\preg_match(
            '/\b(\d+(?:\.\d+)?)\s*km(?=$|\s|[.,;:!?])/i',
            $line,
            $kmMatches,
        )) {
            \assert(isset($kmMatches[1]), 'km must be set at this point');
            $km = $kmMatches[1];
            \assert(\is_numeric($km), 'km must be numeric');

            $distance = (float) $km;
        }

        $mMatches = [];
        if (\preg_match('/\b(\d+)\s*m(?=$|\s|[.,;:!?])/i', $line, $mMatches)) {
            \assert(isset($mMatches[1]), 'm must be set at this point');
            $m = $mMatches[1];
            \assert(\is_numeric($m), 'm must be numeric');

            $elevation = (int) $m;
        }

        $dateMatches = [];
        if (\preg_match(
            '/\b(?:\d{4}-\d{1,2}-\d{1,2}|\d{1,2}\/\d{1,2}\/\d{4})\b/',
            $line,
            $dateMatches,
        )) {
            \assert(isset($dateMatches[0]), 'Date match must not be empty');
            $date = new \DateTime($dateMatches[0]);
        }

        return new SubmissionFeatures($distance, $elevation, $date);
    }
}
