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
class Charity
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[OA\Property]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        nullable: true,
        referencedColumnName: 'uuid',
        name: 'image_uuid',
    )]
    private ?Image $image = null;

    #[OA\Property]
    #[ORM\Column(
        length: 512,
        nullable: true,
    )]
    private ?string $website = null;

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

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
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
            TranslationObjectDto::fromArray(\array_combine(
                \array_map(
                    fn(CharityTranslation $translation): string => $translation->locale,
                    $this->translations->toArray(),
                ),
                \array_map(
                    fn(CharityTranslation $translation): string => $translation->name,
                    $this->translations->toArray(),
                ),
            )),
            TranslationObjectDto::fromArray(\array_combine(
                \array_map(
                    fn(CharityTranslation $translation): string => $translation->locale,
                    $this->translations->toArray(),
                ),
                \array_map(
                    fn(CharityTranslation $translation): string => $translation->description,
                    $this->translations->toArray(),
                ),
            )),
            $this->image?->getPath($imagePath),
            $this->website,
        );
    }
}
