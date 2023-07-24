<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
#[ORM\Index(columns: ['week'], name: 'week_index')]
class Submission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fetchSubmission'])]
    private ?bool $accepted = null;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private ?Season $season = null;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $elevation = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $distance = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?bool $reviewed = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission'])]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $week = null;

    #[ORM\ManyToOne(inversedBy: 'season')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission'])]
    private ?Activity $activity = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['fetchSubmission'])]
    private ?DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faculty $faculty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    public function setElevation(int $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function isReviewed(): ?bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): self
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function calculateWeek(): int
    {
        $sub = $this->getDate()->diff($this->getSeason()->getStart());
        $this->week = intdiv($sub->days, 7);
        return $this->week;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(?Faculty $faculty): static
    {
        $this->faculty = $faculty;

        return $this;
    }
}
