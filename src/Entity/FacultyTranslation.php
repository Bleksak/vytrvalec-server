<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FacultyTranslation
{
    #[ORM\Id]
    #[ORM\Column]
    public private(set) string $locale;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Faculty::class, inversedBy: 'translations', cascade: ['persist'])]
    #[ORM\JoinColumn]
    public Faculty $faculty;

    #[ORM\Column]
    public string $name;

    public function __construct(
        Faculty $faculty,
        string $locale,
        string $name,
    ) {
        $this->faculty = $faculty;
        $this->locale = $locale;
        $this->name = $name;
    }
}
