<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Season\Response\SeasonIndexResponseDto;
use App\Repository\SeasonRepository;
use App\Services\ImagePath;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Index(columns: ['start'], name: 'date_index')]
class Season
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property(example: '2025-04-01')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    #[Groups(['fetchSubmission'])]
    private \DateTime $start;

    #[OA\Property(example: '2025-05-01')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    #[Groups(['fetchSubmission'])]
    private \DateTime $end;

    #[OA\Property]
    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private Charity $charity;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(mappedBy: 'season', targetEntity: Submission::class)]
    private Collection $submissions;

    public function __construct(\DateTime $start, \DateTime $end, Charity $charity)
    {
        $this->submissions = new ArrayCollection();
        $this->start = $start;
        $this->end = $end;
        $this->charity = $charity;
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getCharity(): Charity
    {
        return $this->charity;
    }

    public function setCharity(Charity $charity): self
    {
        $this->charity = $charity;

        return $this;
    }

    /**
     * @return Collection<int, Submission>
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setSeason($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        $this->submissions->removeElement($submission);

        return $this;
    }

    public function canDelete(): bool
    {
        return $this->getSubmissions()->isEmpty();

        // return $this->getStart() >= new DateTimeImmutable('now');
    }

    public function isRunning(): bool
    {
        $today = new \DateTimeImmutable();
        $start = \DateTimeImmutable::createFromInterface($this->getStart());
        $end = \DateTimeImmutable::createFromInterface($this->getEnd());

        return $today >= $start && $today <= $end;
    }

    public function getWeekCount(): int
    {
        $start = \DateTimeImmutable::createFromInterface($this->getStart());
        $end = \DateTimeImmutable::createFromInterface($this->getEnd());

        $weeks = \intdiv($end->diff($start)->days + 1, 7);

        return $weeks === 0 ? 1 : $weeks;
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
        );
    }
}
