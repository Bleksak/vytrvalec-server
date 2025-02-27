<?php

namespace App\Entity;

use App\Repository\ProfileCacheRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ProfileCacheRepository::class)]
class ProfileCache
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'profileCaches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[OA\Property]
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'profileCaches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity;

    #[OA\Property]
    #[ORM\Column]
    private ?int $distance = 0;

    #[OA\Property]
    #[ORM\Column]
    private ?int $elevation = 0;

    public function __construct(
        User $user,
        Activity $activity,
    ) {
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

    /**
     * @param callable(int): int $updateFn
     */
    public function updateDistance(callable $updateFn): ProfileCache
    {
        $this->setDistance(call_user_func($updateFn, $this->getDistance()));

        return $this;
    }

    /**
     * @param callable(int): int $updateFn
     */
    public function updateElevation(callable $updateFn): ProfileCache
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
