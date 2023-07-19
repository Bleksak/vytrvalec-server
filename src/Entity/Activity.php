<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $min_elevation = null;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Submission::class, orphanRemoval: true)]
    private Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: UserSummary::class)]
    private Collection $userSummaries;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: FacultySummary::class)]
    private Collection $facultySummaries;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
        $this->userSummaries = new ArrayCollection();
        $this->facultySummaries = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getMinElevation(): ?int
    {
        return $this->min_elevation;
    }

    public function setMinElevation(int $min_elevation): self
    {
        $this->min_elevation = $min_elevation;

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
            $submission->setActivity($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        if ($this->submissions->removeElement($submission)) {
            // set the owning side to null (unless already changed)
            if ($submission->getActivity() === $this) {
                $submission->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSummary>
     */
    public function getUserSummaries(): Collection
    {
        return $this->userSummaries;
    }

    public function addUserSummary(UserSummary $userSummary): static
    {
        if (!$this->userSummaries->contains($userSummary)) {
            $this->userSummaries->add($userSummary);
            $userSummary->setActivity($this);
        }

        return $this;
    }

    public function removeUserSummary(UserSummary $userSummary): static
    {
        if ($this->userSummaries->removeElement($userSummary)) {
            // set the owning side to null (unless already changed)
            if ($userSummary->getActivity() === $this) {
                $userSummary->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FacultySummary>
     */
    public function getFacultySummaries(): Collection
    {
        return $this->facultySummaries;
    }

    public function addFacultySummary(FacultySummary $facultySummary): static
    {
        if (!$this->facultySummaries->contains($facultySummary)) {
            $this->facultySummaries->add($facultySummary);
            $facultySummary->setActivity($this);
        }

        return $this;
    }

    public function removeFacultySummary(FacultySummary $facultySummary): static
    {
        if ($this->facultySummaries->removeElement($facultySummary)) {
            // set the owning side to null (unless already changed)
            if ($facultySummary->getActivity() === $this) {
                $facultySummary->setActivity(null);
            }
        }

        return $this;
    }
}
