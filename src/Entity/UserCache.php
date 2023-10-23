<?php

namespace App\Entity;

use App\Repository\UserCacheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCacheRepository::class)]
class UserCache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userCache')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $week = null;

    #[ORM\Column]
    private ?int $distance = 0;

    #[ORM\Column]
    private ?int $elevation = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Season $season = null;

    public function __construct(User $user, Activity $activity, Season $season, int $week)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->season = $season;
        $this->week = $week;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

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

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): static
    {
        $this->season = $season;

        return $this;
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
}
