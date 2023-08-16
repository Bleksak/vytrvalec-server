<?php

namespace App\Validation;

class TypeSystem
{
    const CONVERSIONS = [
        'boolean' => ['string'],
        'integer' => ['string', 'boolean', 'double'],
        'double' => ['string'],
        'string' => [],
        'array' => [],
        'object' => [],
        'resource' => [],
        'resource (closed)' => [],
        'NULL' => [],
        'unknown type' => []
    ];

    const REFLECTION = [
        'int' => 'integer',
        'float' => 'double',
        'string' => 'string',
        'bool' => 'boolean',
        'array' => 'array',
        'object' => 'object',
        'mixed' => 'unknown type',
        'number' => 'double',
    ];

    public static function getAvailableConversions(string $type): array
    {
        return self::CONVERSIONS[$type];
    }

    public static function reflectionTypeToBuiltinType(string $reflectionType): string
    {
        return self::REFLECTION[$reflectionType] ?? $reflectionType;
    }

    public static function isConvertibleType(string $type, string $to): bool
    {
        return $type === $to || in_array($to, self::getAvailableConversions($type));
    }

    public static function canAssign(mixed $value, string $to): bool
    {
        $type = is_object($value) ? (new \ReflectionClass($value::class))->getName() : gettype($value);

        if (self::isConvertibleType($type, $to)) {
            return true;
        }

        if ($type === 'string') {
            // string -> double
            if ($to === 'double') {
                return is_numeric($value);
            }

            // string -> boolean
            if ($to === 'boolean') {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
            }

            // string -> int
            if ($to === 'integer') {
                return filter_var($value, FILTER_VALIDATE_INT) !== false;
            }
        }

        // double => int
        if ($type === 'double') {
            return intval($value) == $value;
        }

        // int => bool
        if ($type === 'integer') {
            return $value === 0 || $value === 1;
        }

        return false;
    }
}