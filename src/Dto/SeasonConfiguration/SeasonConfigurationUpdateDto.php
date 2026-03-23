<?php

declare(strict_types=1);

namespace App\Dto\SeasonConfiguration;

use App\Dto\Faculty\FacultyMappingDto;
use App\Dto\Season\SeasonUpdateDto;
use OpenApi\Attributes as OA;

final readonly class SeasonConfigurationUpdateDto
{
    /** @param list<FacultyMappingDto> $facultyMapping */
    public function __construct(
        #[OA\Property]
        public SeasonUpdateDto $season,

        #[OA\Property]
        public array $facultyMapping,
    ) {}
}
