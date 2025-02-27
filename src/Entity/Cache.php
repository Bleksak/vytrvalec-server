<?php

namespace App\Entity;

use App\Dto\WeeklyResultDto;
use App\Repository\SeasonCacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonCacheRepository::class)]
class Cache
{
    #[ORM\Id]
    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Season $season;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, columnDefinition: 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP')]
    private \DateTimeInterface $createdAt;

    /**
     * @var array<int, WeeklyResultDto> $data
     */
    #[ORM\Column]
    private array $data;

    /**
     * @param array<int, WeeklyResultDto> $data
     */
    public function __construct(Season $season, array $data)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->season = $season;
        $this->data = $data;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<int, WeeklyResultDto> $data
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
