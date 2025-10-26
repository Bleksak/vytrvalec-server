<?php

declare(strict_types=1);

namespace App\Dto\SeasonConfiguration;

use App\Dto\Charity\CharityCreateDto;
use App\Dto\Faculty\FacultyMappingDto;
use App\Dto\Season\SeasonCreateDto;
use OpenApi\Attributes as OA;

final readonly class SeasonConfigurationCreateDto
{
    /** @param list<FacultyMappingDto> $facultyMapping */
    public function __construct(
        #[OA\Property]
        public CharityCreateDto $charity,

        #[OA\Property]
        public SeasonCreateDto $season,

        #[OA\Property]
        public array $facultyMapping,
    ) {}
}
