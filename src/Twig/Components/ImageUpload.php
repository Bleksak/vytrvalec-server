<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ImageUpload
{
    use DefaultActionTrait;

    public ?string $image = null;

    #[LiveProp]
    public bool $disabled = false;

    public function __construct() {}

    public function mount(?string $image = null, bool $disabled = false): void
    {
        $this->image = $image;
        $this->disabled = $disabled;
    }

    #[LiveListener('updateImage')]
    public function updateImage(#[LiveArg('image')] ?string $image): void
    {
        $this->image = $image;
    }
}
