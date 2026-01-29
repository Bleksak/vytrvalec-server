<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Faculty\Response\FacultyMappingResponseDto;
use App\Repository\FacultyMappingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacultyMappingRepository::class)]
class FacultyMapping
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'facultyMappings')]
    #[ORM\JoinColumn]
    public Season $season;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn]
    public Faculty $faculty;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn]
    public ?Faculty $parent = null;

    public function __construct(
        Season $season,
        Faculty $faculty,
        ?Faculty $parent = null,
    ) {
        $this->season = $season;
        $this->faculty = $faculty;
        $this->parent = $parent;
    }

    public function toResponseObject(): FacultyMappingResponseDto
    {
        return new FacultyMappingResponseDto(
            $this->season->id,
            $this->faculty->id,
            $this->parent?->id,
        );
    }
}
