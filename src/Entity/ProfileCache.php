<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Statistics\ProfileCacheResponseDto;
use App\Repository\ProfileCacheRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ProfileCacheRepository::class)]
final class ProfileCache
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'profileCaches')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $user;

    #[OA\Property]
    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Activity $activity;

    #[OA\Property]
    #[ORM\Column]
    public int $distance = 0;

    #[OA\Property]
    #[ORM\Column]
    public int $elevation = 0;

    public function __construct(User $user, Activity $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    public function toResponseObject(): ProfileCacheResponseDto
    {
        return new ProfileCacheResponseDto(
            $this->activity->id,
            $this->distance,
            $this->elevation,
        );
    }
}
