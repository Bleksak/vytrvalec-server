<?php

namespace App\Entity;

use App\Repository\FacultyRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FacultyRepository::class)]
class Faculty
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission', 'fetchFacultySummary', 'fetchSeasonResult', 'fetchUser'])]
    private ?int $id = null;

    #[OA\Property(example: 'Fakulta aplikovaných věd')]
    #[ORM\Column(length: 255)]
    #[Groups(['fetchSubmission', 'fetchFacultySummary', 'fetchSeasonResult', 'fetchUser'])]
    private ?string $name = null;

    #[OA\Property(example: 'FAV')]
    #[ORM\Column(length: 10)]
    #[Groups(['fetchSubmission', 'fetchFacultySummary', 'fetchSeasonResult', 'fetchUser'])]
    private ?string $shortcut = null;

    #[OA\Parameter(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?bool $visible = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    public function __construct(
        string $name,
        string $shortcut,
        bool $visible,
    ) {
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->visible = $visible;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function setShortcut(string $shortcut): self
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }
}
