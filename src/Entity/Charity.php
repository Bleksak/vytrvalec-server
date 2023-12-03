<?php

namespace App\Entity;

use App\Repository\CharityRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharityRepository::class)]
class Charity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $name = null;

    #[ORM\Column(length: 10000)]
    #[Groups(['fetchSubmission', 'fetchSeasonList', 'fetchSeasonResult', 'fetchCharity'])]
    private ?string $description = null;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
