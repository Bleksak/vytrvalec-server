<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Faculty;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class FacultyTag
{
    public Faculty $faculty;

    public function mount(Faculty $faculty): void
    {
        $this->faculty = $faculty;
    }
}
