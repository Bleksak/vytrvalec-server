<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Charity\CharityCreateTranslationDto;
use App\Dto\Charity\Response\CharityGetResponseDto;
use App\Dto\TranslationObjectDto;
use App\Repository\CharityRepository;
use App\Services\ImagePath;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: CharityRepository::class)]
final class Charity extends AbstractEntity
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[OA\Property]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        nullable: true,
        referencedColumnName: 'uuid',
        name: 'image_uuid',
    )]
    public ?Image $image = null;

    #[OA\Property]
    #[ORM\Column(length: 512, nullable: true)]
    public ?string $website = null;

    /** @var Collection<string, CharityTranslation> */
    #[ORM\OneToMany(
        mappedBy: 'charity',
        targetEntity: CharityTranslation::class,
        cascade: ['persist', 'remove'],
        indexBy: 'locale',
    )]
    public Collection $translations;

    public function __construct(
        CharityCreateTranslationDto $translations,
        ?Image $image = null,
        ?string $website = null,
    ) {
        $this->image = $image;
        $this->website = $website;
        $this->translations = new ArrayCollection();

        $descriptionTranslations = $translations->description->toArray();

        foreach ($translations->name->toArray() as $locale => $translation) {
            \assert($translation !== null, 'Preklad nesmi byt null');

            $this->addTranslation(
                new CharityTranslation(
                    $this,
                    $locale,
                    $translation,
                    $descriptionTranslations[$locale] ?? '',
                ),
            );
        }
    }

    public function addTranslation(CharityTranslation $translation): void
    {
        if (!$this->translations->containsKey($translation->locale)) {
            $this->translations->set($translation->locale, $translation);
        }
    }

    public function toResponseObject(?ImagePath $imagePath = null): CharityGetResponseDto
    {
        return new CharityGetResponseDto(
            $this->id ?? 0,
            TranslationObjectDto::fromArray(\array_column(
                $this->translations->toArray(),
                'name',
                'locale',
            )),
            TranslationObjectDto::fromArray(\array_column(
                $this->translations->toArray(),
                'description',
                'locale',
            )),
            $this->image?->getPath($imagePath),
            $this->website,
        );
    }
}
