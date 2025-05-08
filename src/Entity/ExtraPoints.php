<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExtraPointsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExtraPointsRepository::class)]
class ExtraPoints
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fetchSeasonResult'])]
    private string $name;

    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private int $points;

    #[ORM\Column]
    #[Groups(['fetchSeasonResult'])]
    private int $week;

    public function __construct(
        string $name,
        int $points,
        int $week,
    ) {
        $this->name = $name;
        $this->points = $points;
        $this->week = $week;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getWeek(): int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }
}
