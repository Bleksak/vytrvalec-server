<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class FacultyTranslation
{
    #[ORM\Id]
    #[ORM\ManyToOne(
        targetEntity: Faculty::class,
        inversedBy: 'translations',
        cascade: ['persist'],
    )]
    #[ORM\JoinColumn]
    public Faculty $faculty;

    #[ORM\Id]
    #[ORM\Column(length: 6)]
    public private(set) string $locale;

    #[ORM\Column]
    public string $name;

    public function __construct(Faculty $faculty, string $locale, string $name)
    {
        $this->faculty = $faculty;
        $this->locale = $locale;
        $this->name = $name;
    }
}
