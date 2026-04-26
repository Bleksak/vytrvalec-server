<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Faculty\Response\FacultyMappingResponseDto;
use App\Dto\Season\Response\SeasonIndexResponseDto;
use App\Repository\SeasonRepository;
use App\Services\ImagePath;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Index(columns: ['start'], name: 'date_index')]
final class Season extends AbstractEntity
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[OA\Property(example: '2025-04-01')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    public \DateTime $start;

    #[OA\Property(example: '2025-05-01')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    public \DateTime $end;

    #[OA\Property]
    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    public Charity $charity;

    #[OA\Property]
    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $isTest = false;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(mappedBy: 'season', targetEntity: Submission::class)]
    public Collection $submissions;

    /**
     * @var Collection<int, FacultyMapping>
     */
    #[ORM\OneToMany(
        targetEntity: FacultyMapping::class,
        mappedBy: 'season',
        orphanRemoval: true,
    )]
    public Collection $facultyMappings;

    public function __construct(
        \DateTime $start,
        \DateTime $end,
        Charity $charity,
        bool $isTest,
    ) {
        $this->submissions = new ArrayCollection();
        $this->start = $start;
        $this->end = $end;
        $this->charity = $charity;
        $this->isTest = $isTest;
        $this->facultyMappings = new ArrayCollection();
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->season = $this;
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        $this->submissions->removeElement($submission);

        return $this;
    }

    /**
     * WARNING: This method is not safe to use, when the submissions are not already fetched. It will cause another query to the database.
     */
    public function canDelete(): bool
    {
        if ($this->isTest) {
            return true;
        }

        return $this->submissions->isEmpty();
    }

    public function isRunning(): bool
    {
        $today = new \DateTime();
        $today->setTime(0, 0);

        $start = \DateTimeImmutable::createFromInterface($this->start);
        $end = \DateTimeImmutable::createFromInterface($this->end);

        return $today >= $start && $today <= $end;
    }

    public function getWeekCount(): int
    {
        $start = \DateTimeImmutable::createFromInterface($this->start);
        $end = \DateTimeImmutable::createFromInterface($this->end);

        $diff = $end->diff($start);
        \assert(
            $diff->days !== false,
            'DateInterval vytvoreny pres diff nemuze mit days = false',
        );

        $weeks = \intdiv($diff->days + 1, 7);

        return $weeks === 0 ? 1 : $weeks;
    }

    public function addFacultyMapping(FacultyMapping $facultyMapping): static
    {
        if (!$this->facultyMappings->contains($facultyMapping)) {
            $this->facultyMappings->add($facultyMapping);
            $facultyMapping->season = $this;
        }

        return $this;
    }

    public function removeFacultyMapping(FacultyMapping $facultyMapping): static
    {
        $this->facultyMappings->removeElement($facultyMapping);

        return $this;
    }

    public function toResponseObject(?ImagePath $imagePath = null): SeasonIndexResponseDto
    {
        return new SeasonIndexResponseDto(
            $this->id ?? 0,
            $this->charity->toResponseObject($imagePath),
            $this->start,
            $this->end,
            $this->canDelete(),
            $this->isRunning(),
            $this->isTest,
            \array_map(
                static fn(FacultyMapping $mapping): FacultyMappingResponseDto => $mapping->toResponseObject(),
                $this->facultyMappings->toArray(),
            ),
        );
    }
}
