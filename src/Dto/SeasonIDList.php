<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final class SeasonIDList
{
    /**
     * @var array<int>
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'integer'))]
    public array $seasons = [];
}
