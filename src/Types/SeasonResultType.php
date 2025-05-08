<?php

declare(strict_types=1);

namespace App\Types;

use App\Dto\SeasonResultDto;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class SeasonResultType extends Type
{
    public const NAME = 'season_result';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Invalid value, JSON string expected');
        }

        /**
         * @var SeasonResultDto $seasonResultData
         */
        $seasonResultData = json_decode($value);

        if (!isset($seasonResultData->results, $seasonResultData->outliers)) {
            throw new InvalidArgumentException('Invalid value, resutls and outliers expected');
        }

        return new SeasonResultDto(
            $seasonResultData->results,
            $seasonResultData->outliers,
        );
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
    }
}
