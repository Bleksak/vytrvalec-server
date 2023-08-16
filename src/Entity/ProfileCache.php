<?php

namespace App\Entity;

use App\Repository\ProfileCacheRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfileCacheRepository::class)]
class ProfileCache
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'profileCaches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'profileCaches')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userProfile'])]
    private ?Activity $activity;

    #[ORM\Column]
    #[Groups(['userProfile'])]
    private ?int $distance = 0;

    #[ORM\Column]
    #[Groups(['userProfile'])]
    private ?int $elevation = 0;

    public function __construct(User $user, Activity $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function updateDistance(callable $updateFn): static
    {
        $this->setDistance(call_user_func($updateFn, $this->getDistance()));
        return $this;
    }

    public function updateElevation(callable $updateFn): static
    {
        $this->setElevation(call_user_func($updateFn, $this->getElevation()));
        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    public function setElevation(int $elevation): static
    {
        $this->elevation = $elevation;

        return $this;
    }
}
