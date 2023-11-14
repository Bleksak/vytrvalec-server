<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'userProfile', 'fetchSeasonResult', 'fetchActivity'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission', 'userProfile', 'fetchSeasonResult', 'fetchActivity'])]
    private string $name;

    #[ORM\Column]
    #[Groups(['fetchSubmission', 'userProfile', 'fetchActivity'])]
    private bool $active = true;

    #[ORM\Column]
    #[Groups(['fetchSubmission', 'userProfile', 'fetchActivity'])]
    private int $minElevation;

    public function __construct(string $name, int $minElevation)
    {
        $this->name = $name;
        $this->minElevation = $minElevation;
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
}
