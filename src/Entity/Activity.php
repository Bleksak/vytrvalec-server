<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Activity\Response\ActivityResponseDto;
use App\Repository\ActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity implements Translatable
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property(example: 'Běh a chůze')]
    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    #[Groups(['fetchSubmission'])]
    private string $name;

    #[OA\Property(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private bool $active = true;

    #[OA\Property(example: 1000)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private int $minElevation;

    public function __construct(
        string $name,
        int $minElevation,
    ) {
        $this->name = $name;
        $this->minElevation = $minElevation;
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getMinElevation(): int
    {
        return $this->minElevation;
    }

    public function setMinElevation(int $minElevation): self
    {
        $this->minElevation = $minElevation;

        return $this;
    }

    public function toResponseObject(): ActivityResponseDto
    {
        return new ActivityResponseDto(
            $this->getId(),
            $this->getName(),
            $this->isActive(),
            $this->getMinElevation(),
        );
    }
}
