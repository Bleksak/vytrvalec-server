<?php

declare(strict_types=1);

namespace App\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

final class WeeklyResultDto
{
    /**
     * @param array<int,ActivityResultDto> $activities
     */
    public function __construct(
        #[OA\Property(example: 2)]
        public readonly int $week,
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ActivityResultDto::class)))]
        public array $activities,
    ) {}
}
