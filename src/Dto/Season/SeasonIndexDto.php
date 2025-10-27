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
            $this->season->getId(),
            $this->season->getCharity()->toResponseObject($imagePath),
            $this->season->getStart(),
            $this->season->getEnd(),
            $this->canDelete,
            $this->season->isRunning(),
            \array_map(
                static fn(FacultyMapping $mapping): FacultyMappingResponseDto => $mapping->toResponseObject(),
                $this->season->getFacultyMappings()->toArray(),
            ),
        );
    }
}
