<?php

declare(strict_types=1);

namespace App\Utils;

abstract class AbstractProperty
{
    public static function isInitialized(object $class, string $field): bool
    {
        $property = new \ReflectionProperty($class::class, $field);

        return $property->isInitialized($class);
    }
}
