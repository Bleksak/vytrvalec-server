<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class ImagePath
{
    private string $applicationPath;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->applicationPath = $parameterBag->get('app_base');
    }

    public function fullPath(string|Image $image): string
    {
        if (is_string($image)) {
            return $this->applicationPath.$image;
        }

        return $this->applicationPath.$image->getPath();
    }
}
