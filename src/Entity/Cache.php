<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\SeasonResultDto;
use App\Repository\SeasonCacheRepository;
use App\Types\SeasonResultType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonCacheRepository::class)]
final class Cache
{
    #[ORM\Id]
    #[ORM\OneToOne]
    #[ORM\JoinColumn]
    public Season $season;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public \DateTime $createdAt;

    #[ORM\Column(type: SeasonResultType::NAME)]
    public SeasonResultDto $data;

    public function __construct(Season $season, SeasonResultDto $data)
    {
        $this->createdAt = new \DateTime();
        $this->season = $season;
        $this->data = $data;
    }
}
