<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Faculty\FacultyCreateTranslationDto;
use App\Dto\Faculty\Response\FacultyResponseDto;
use App\Dto\TranslationObjectDto;
use App\Repository\FacultyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[Groups(['fetchSubmission'])]
    public private(set) int $id;

    #[OA\Property(example: 'FAV')]
    #[ORM\Column(length: 10)]
    #[Groups(['fetchSubmission'])]
    public string $shortcut;

    #[OA\Parameter(example: true)]
    #[ORM\Column]
    #[Groups(['fetchSubmission'])]
    public bool $visible;

    #[ORM\ManyToOne(targetEntity: self::class)]
    public ?Faculty $parent = null;

    #[ORM\Column(length: 8)]
    public string $color;

    /** @var Collection<string, FacultyTranslation> */
    #[ORM\OneToMany(mappedBy: 'faculty', targetEntity: FacultyTranslation::class, cascade: ['persist', 'remove'], indexBy: 'locale')]
    public Collection $translations;

    public function __construct(
        FacultyCreateTranslationDto $translations,
        string $shortcut,
        bool $visible,
        string $color,
    ) {
        $this->shortcut = $shortcut;
        $this->visible = $visible;

        $this->translations = new ArrayCollection();
        $this->color = $color;

        foreach ($translations->name->toArray() as $locale => $value) {
            assert($value !== null, 'Hodnota překladu nesmí být null');

            $this->addTranslation(new FacultyTranslation($this, $locale, $value));
        }
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function addTranslation(FacultyTranslation $translation): void
    {
        if (!$this->translations->containsKey($translation->locale)) {
            $this->translations->set($translation->locale, $translation);
        }
    }

    public function toResponseObject(): FacultyResponseDto
    {
        return new FacultyResponseDto(
            $this->id,
            TranslationObjectDto::fromArray(array_column($this->translations->toArray(), 'name', 'locale')),
            $this->shortcut,
            $this->visible,
            $this->parent?->id,
            $this->color,
        );
    }
}
