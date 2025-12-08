<?php

declare(strict_types=1);

namespace App\Dto\Activity;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

final class ActivityCreateDto
{
    #[OA\Property]
    public ActivityCreateTranslationDto $translations;

    #[OA\Property]
    public Uuid $icon;

    #[OA\Property(
        example: 1500,
        description: 'Minimum elevation to be eligible to get extra points (in meters).',
    )]
    public int $minElevation;
}
