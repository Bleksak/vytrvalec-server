<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class CharityTranslation
{
    #[ORM\Id]
    #[ORM\ManyToOne(
        targetEntity: Charity::class,
        inversedBy: 'translations',
        cascade: ['persist'],
    )]
    #[ORM\JoinColumn]
    public Charity $charity;

    #[ORM\Id]
    #[ORM\Column(length: 6)]
    public private(set) string $locale;

    #[ORM\Column]
    public string $name;

    #[ORM\Column(length: 10_000)]
    public string $description;

    public function __construct(
        Charity $charity,
        string $locale,
        string $name,
        string $description,
    ) {
        $this->charity = $charity;
        $this->locale = $locale;
        $this->name = $name;
        $this->description = $description;
    }
}
