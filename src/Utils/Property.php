<?php

namespace App\Utils;

abstract class Property
{
    public static function isInitialized(mixed $class, string $field): bool
    {
        $property = new \ReflectionProperty($class::class, $field);

        return $property->isInitialized($class);
    }
}
