<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExtraPointsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExtraPointsRepository::class)]
final class ExtraPoints
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column]
    public int $points;

    #[ORM\Column]
    public int $week;

    public function __construct(string $name, int $points, int $week)
    {
        $this->name = $name;
        $this->points = $points;
        $this->week = $week;
    }
}
