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
    #[Groups(['fetchSubmission', 'userProfile', 'fetchSeasonResult'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission', 'userProfile', 'fetchSeasonResult'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission', 'userProfile'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['fetchSubmission', 'userProfile'])]
    private ?int $minElevation = null;

    #[ORM\OneToMany(mappedBy: 'faculty', targetEntity: FacultyCache::class)]
    private Collection $facultyCaches;

    public function __construct()
    {
        $this->facultyCaches = new ArrayCollection();
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
        return $this->minElevation;
    }

    public function setMinElevation(int $minElevation): self
    {
        $this->minElevation = $minElevation;

        return $this;
    }

    public function getFacultyCaches(): ?Collection
    {
        return $this->facultyCaches;
    }
}
