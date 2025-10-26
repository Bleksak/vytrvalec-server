<?php

declare(strict_types=1);

namespace App\Dto\Faculty\Response;

final readonly class FacultyMappingResponseDto
{
    public function __construct(
        public int $seasonId,
        public int $facultyId,
        public ?int $parentId,
    ) {}
}
