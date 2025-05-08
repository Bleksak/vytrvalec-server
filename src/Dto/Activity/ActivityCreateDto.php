<?php

declare(strict_types=1);

namespace App\Dto\Activity;

use OpenApi\Attributes as OA;

final class ActivityCreateDto
{
    #[OA\Property(example: 'Běh a chůze')]
    public string $name;

    #[OA\Property(example: 1500, description: 'Minimum elevation to be eligible to get extra points (in meters).')]
    public int $minElevation;
}
