<?php

declare(strict_types=1);

namespace App\Dto\Season;

use App\Dto\Faculty\Response\FacultyMappingResponseDto;
use App\Dto\Season\Response\SeasonIndexResponseDto;
use App\Entity\FacultyMapping;
use App\Entity\Season;
use App\Services\ImagePath;

final readonly class SeasonIndexDto
{
    public function __construct(
        public Season $season,
        public bool $canDelete,
    ) {}

    public function toResponseObject(?ImagePath $imagePath = null): SeasonIndexResponseDto
    {
        return new SeasonIndexResponseDto(
            $this->season->id,
            $this->season->charity->toResponseObject($imagePath),
            $this->season->start,
            $this->season->end,
            $this->canDelete,
            $this->season->isRunning(),
            \array_map(
                static fn(FacultyMapping $mapping): FacultyMappingResponseDto => $mapping->toResponseObject(),
                $this->season->facultyMappings->toArray(),
            ),
        );
    }
}
