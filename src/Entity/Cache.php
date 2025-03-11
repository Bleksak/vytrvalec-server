<?php

namespace App\Entity;

use App\Dto\SeasonResultDto;
use App\Repository\SeasonCacheRepository;
use App\Types\SeasonResultType;
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

    #[ORM\Column(type: SeasonResultType::NAME)]
    private SeasonResultDto $data;

    public function __construct(Season $season, SeasonResultDto $data)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->season = $season;
        $this->data = $data;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function getData(): SeasonResultDto
    {
        return $this->data;
    }

    public function setData(SeasonResultDto $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
