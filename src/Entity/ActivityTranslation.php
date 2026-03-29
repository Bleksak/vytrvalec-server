<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class ActivityTranslation extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(length: 6)]
    public private(set) string $locale;

    #[ORM\Id]
    #[ORM\ManyToOne(
        targetEntity: Activity::class,
        inversedBy: 'translations',
        cascade: ['persist'],
    )]
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
