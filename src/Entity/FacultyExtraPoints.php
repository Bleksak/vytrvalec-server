<?php

namespace App\Entity;

use App\Repository\FacultyExtraPointsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FacultyExtraPointsRepository::class)]
class FacultyExtraPoints
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'extraPoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FacultyCache $cache = null;

    #[ORM\Id]
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'extraPoints')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fetchSeasonResult'])]
    private ?ExtraPoints $extraPoints = null;

    #[ORM\Column]
    private ?int $value = null;

    public function getCache(): ?FacultyCache
    {
        return $this->cache;
    }

    public function setCache(?FacultyCache $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExtraPoints(): ?ExtraPoints
    {
        return $this->extraPoints;
    }

    public function setExtraPoints(?ExtraPoints $extraPoints): static
    {
        $this->extraPoints = $extraPoints;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }
}
