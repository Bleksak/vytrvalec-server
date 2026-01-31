<?php

declare(strict_types=1);

namespace App\Dto\Season\Response;

use App\Dto\Charity\Response\CharityGetResponseDto;
use App\Dto\Faculty\Response\FacultyMappingResponseDto;

final readonly class SeasonIndexResponseDto
{
    /**
     * TODO(@bleksak): when possible, change to list<FacultyMappingResponseDto>
     *
     * @param array<FacultyMappingResponseDto> $facultyMapping
     */
    public function __construct(
        public int $id,
        public CharityGetResponseDto $charity,
        public \DateTime $start,
        public \DateTime $end,
        public bool $canDelete,
        public bool $isRunning,
        public bool $isTest,
        public array $facultyMapping,
    ) {}
}
