<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Charity\CharityCreateTranslationDto;
use App\Dto\Charity\Response\CharityGetResponseDto;
use App\Dto\TranslationObjectDto;
use App\Repository\CharityRepository;
use App\Services\ImagePath;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CharityRepository::class)]
class Charity
{
    #[OA\Property]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    private ?int $id = null;

    #[OA\Property]
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'uuid', name: 'image_uuid')]
    #[Groups(['fetchSubmission'])]
    private ?Image $image = null;

    #[OA\Property]
    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['fetchSubmission'])]
    private ?string $website = null;

    /** @var Collection<string, CharityTranslation> */
    #[ORM\OneToMany(mappedBy: 'charity', targetEntity: CharityTranslation::class, cascade: ['persist', 'remove'], indexBy: 'locale')]
    public Collection $translations;

    public function __construct(
        CharityCreateTranslationDto $translations,
        ?Image $image = null,
        ?string $website = null,
    ) {
        $this->image = $image;
        $this->website = $website;

        $descriptionTranslations = $translations->description->toArray();

        foreach ($translations->name->toArray() as $locale => $translation) {
            assert($translation !== null, 'Preklad nesmi byt null');

            $this->addTranslation(
                new CharityTranslation(
                    $this,
                    $locale,
                    $translation,
                    $descriptionTranslations[$locale] ?? ''
                )
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
            TranslationObjectDto::fromArray(array_column($this->translations->toArray(), 'locale', 'name')),
            TranslationObjectDto::fromArray(array_column($this->translations->toArray(), 'locale', 'description')),
            $this->image?->getPath($imagePath),
            $this->website,
        );
    }
}
