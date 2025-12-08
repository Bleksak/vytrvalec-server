<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CharityTranslation
{
    #[ORM\Id]
    #[ORM\Column]
    public private(set) string $locale;

    #[ORM\Id]
    #[ORM\ManyToOne(
        targetEntity: Charity::class,
        inversedBy: 'translations',
        cascade: ['persist'],
    )]
    #[ORM\JoinColumn]
    public Charity $charity;

    #[ORM\Column]
    public string $name;

    #[ORM\Column]
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
