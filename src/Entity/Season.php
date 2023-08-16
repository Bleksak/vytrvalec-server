<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Index(columns: ['start'], name: 'date_index')]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchFacultySummary'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchFacultySummary'])]
    private ?DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchFacultySummary'])]
    private ?DateTimeInterface $end = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchFacultySummary'])]
    private ?Charity $charity = null;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(mappedBy: 'season', targetEntity: Submission::class)]
    private Collection $submissions;

//    #[ORM\OneToMany(mappedBy: 'season', targetEntity: FacultyCache::class)]
//    private Collection $activityCaches;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
//        $this->activityCaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getCharity(): ?Charity
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
        if ($this->submissions->removeElement($submission)) {
            // set the owning side to null (unless already changed)
            if ($submission->getSeason() === $this) {
                $submission->setSeason(null);
            }
        }

        return $this;
    }

    public function canDelete(): bool
    {
        return $this->getStart() >= new DateTimeImmutable('now');
    }
    
    public function isRunning(): bool
    {
        $today = new DateTimeImmutable();
        $start = DateTimeImmutable::createFromInterface($this->getStart());
        $end = DateTimeImmutable::createFromInterface($this->getEnd());

        return $today >= $start && $today < $end;
    }

    /**
     * @return Collection<int, FacultyCache>
     */
//    public function getActivityCaches(): Collection
//    {
//        return $this->activityCaches;
//    }
//
//    public function addActivityCache(FacultyCache $activityCache): static
//    {
//        if (!$this->activityCaches->contains($activityCache)) {
//            $this->activityCaches->add($activityCache);
//            $activityCache->setSeason($this);
//        }
//
//        return $this;
//    }
//
//    public function removeActivityCache(FacultyCache $activityCache): static
//    {
//        if ($this->activityCaches->removeElement($activityCache)) {
//            // set the owning side to null (unless already changed)
//            if ($activityCache->getSeason() === $this) {
//                $activityCache->setSeason(null);
//            }
//        }
//
//        return $this;
//    }
}
