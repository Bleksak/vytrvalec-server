<?php

namespace App\Dto;

use App\Entity\Charity;
use OpenApi\Attributes as OA;

final class SeasonDto
{
    #[OA\Property(example: '2025-04-01')]
    public ?\DateTime $start;

    #[OA\Property(example: '2025-05-01')]
    public ?\DateTime $end;

    #[OA\Property(type: 'integer', example: '1')]
    public ?Charity $charity;
}
