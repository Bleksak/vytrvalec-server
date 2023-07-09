<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiResource
{
    public function __construct(private string $resourceName)
    {
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }
}