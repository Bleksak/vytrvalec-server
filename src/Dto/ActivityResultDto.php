<?php

namespace App\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

final class ActivityResultDto
{
    /**
     * @var array<ExtraPointsDto>
     **/
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ExtraPointsDto::class)))]
    public array $extras = [];

    /**
     * @param array<int, FacultyResultDto> $results
     **/
    public function __construct(
        #[OA\Property(example: 1)]
        public readonly int $activity,

        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: FacultyResultDto::class)))]
        public readonly array $results,
    ) {
    }
}
