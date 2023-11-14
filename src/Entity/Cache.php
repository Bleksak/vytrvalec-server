<?php

namespace App\Entity;

use App\Dto\ActivityResultDto;
use App\Repository\CacheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheRepository::class)]
class Cache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Season $season;

    #[ORM\Column]
    private array $data;
    /**
     * @param array<int, array<ActivityResultDto>> $data
     */
    public function __construct(Season $season, array $data)
    {
        $this->season = $season;
        $this->data = $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
