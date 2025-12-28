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

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
final class Activity
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[OA\Property]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        nullable: true,
        referencedColumnName: 'uuid',
        name: 'icon_uuid',
    )]
    public ?Image $icon;

    #[OA\Property(example: true)]
    #[ORM\Column]
    public bool $active = true;

    #[OA\Property(example: 1000)]
    #[ORM\Column]
    public int $minElevation;

    /** @var Collection<string, ActivityTranslation> */
    #[ORM\OneToMany(
        mappedBy: 'activity',
        targetEntity: ActivityTranslation::class,
        cascade: ['persist', 'remove'],
        indexBy: 'locale',
    )]
    public Collection $translations;

    public function __construct(
        ActivityCreateTranslationDto $translations,
        int $minElevation,
        Image $icon,
    ) {
        $this->minElevation = $minElevation;
        $this->icon = $icon;
        $this->translations = new ArrayCollection();

        foreach ($translations->name->toArray() as $locale => $value) {
            \assert($value !== null, 'Hodnota překladu nesmí být null');

            $this->addTranslation(new ActivityTranslation(
                $this,
                $locale,
                $value,
            ));
        }
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
            $this->id,
            TranslationObjectDto::fromArray(\array_column(
                $this->translations->toArray(),
                'name',
                'locale',
            )),
            $this->icon?->getPath($imagePath),
            $this->active,
            $this->minElevation,
        );
    }
}
