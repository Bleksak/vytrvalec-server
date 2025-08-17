<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ActivityTranslation
{
    #[ORM\Id]
    #[ORM\Column]
    public private(set) string $locale;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'translations', cascade: ['persist'])]
    #[ORM\JoinColumn]
    public Activity $activity;

    #[ORM\Column]
    public string $name;

    public function __construct(
        Activity $activity,
        string $locale,
        string $name,
    ) {
        $this->activity = $activity;
        $this->locale = $locale;
        $this->name = $name;
    }
}
