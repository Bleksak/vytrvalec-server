<?php

declare(strict_types=1);

namespace App\Dto\Season;

use App\Entity\Charity;
use OpenApi\Attributes as OA;

final class SeasonCreateDto
{
    #[OA\Property(example: '2025-04-01')]
    public \DateTime $start;

    #[OA\Property(example: '2025-05-01')]
    public \DateTime $end;

    #[OA\Property(type: 'integer', example: '1')]
    public Charity $charity;
}
