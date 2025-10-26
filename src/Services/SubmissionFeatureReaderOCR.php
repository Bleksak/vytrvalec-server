<?php

declare(strict_types=1);

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractNotFoundException;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

final readonly class SubmissionFeatureReaderOCR
{
    public static function readSubmissionImage(string $image): ?string
    {
        try {
            /** @var string */
            return new TesseractOCR($image)
                ->psm(12) // @mago-expect analysis: non-documented-method
                ->oem(3) // @mago-expect analysis: mixed-method-access
                ->dpi(300) // @mago-expect analysis: mixed-method-access
                ->lang('eng+ces') // @mago-expect analysis: mixed-method-access
                ->config('classify_bln_numeric_mode', '1') // @mago-expect analysis: mixed-method-access
                ->config('segment_segcost_rating', '0.1') // @mago-expect analysis: mixed-method-access
                ->run(); // @mago-expect analysis: mixed-method-access
        } catch (TesseractOcrException|TesseractNotFoundException) {
            return null;
        }
    }
}
