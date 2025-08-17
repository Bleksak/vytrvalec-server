<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Activity\ActivityCreateTranslationDto;
use App\Dto\Activity\Response\ActivityResponseDto;
use App\Dto\TranslationObjectDto;
use App\Repository\ActivityRepository;
use App\Services\ImagePath;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'uuid', name: 'icon_uuid')]
    private ?Image $icon;

    #[OA\Property(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private bool $active = true;

    #[OA\Property(example: 1000)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private int $minElevation;

    /** @var Collection<string, ActivityTranslation> */
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: ActivityTranslation::class, cascade: ['persist', 'remove'], indexBy: 'locale')]
    private Collection $translations;

    public function __construct(ActivityCreateTranslationDto $translations, int $minElevation, Image $icon)
    {
        $this->minElevation = $minElevation;
        $this->icon = $icon;
        $this->translations = new ArrayCollection();

        foreach ($translations->name->toArray() as $locale => $value) {
            assert($value !== null, 'Hodnota překladu nesmí být null');

            $this->addTranslation(new ActivityTranslation($this, $locale, $value));
        }
    }

    public function getId(): int
    {
        return $this->id ?? 0;
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

    public function setIcon(?Image $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?Image
    {
        return $this->icon;
    }

    /**
     * @return Collection<string, ActivityTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(ActivityTranslation $translation): void
    {
        if (!$this->translations->containsKey($translation->locale)) {
            $this->translations->set($translation->locale, $translation);
        }
    }

    public function toResponseObject(?ImagePath $imagePath): ActivityResponseDto
    {
        return new ActivityResponseDto(
            $this->getId(),
            TranslationObjectDto::fromArray(array_column($this->translations->toArray(), 'name', 'locale')),
            $this->getIcon()?->getPath($imagePath),
            $this->isActive(),
            $this->getMinElevation(),
        );
    }
}
