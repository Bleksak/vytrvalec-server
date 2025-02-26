<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

final class CharityDto
{
    #[OA\Property(type: 'string', example: 'David a Goliáš')]
    public ?string $name = null;

    #[OA\Property(type: 'string', example: 'Krátký text o charitě')]
    public ?string $description = '';
}
