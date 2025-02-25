<?php

namespace App\Entity;

use App\Dto\ActivityResultDto;
use App\Repository\CacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheRepository::class)]
class Cache
{
    #[ORM\Id]
    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Season $season;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column]
    private array $data;

    /**
     * @param array<int, array<ActivityResultDto>> $data
     */
    public function __construct(Season $season, array $data)
    {
        $this->createdAt = new \DateTimeImmutable();
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

    /**
     * @param array<int, array<ActivityResultDto>> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
