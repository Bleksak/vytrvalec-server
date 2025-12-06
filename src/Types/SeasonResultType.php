<?php

declare(strict_types=1);

namespace App\Types;

use App\Dto\SeasonResultDto;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @import-type SeasonResultDtoType from SeasonResultDto
 */
final class SeasonResultType extends Type
{
    public const string NAME = 'season_result';

    /**
     * @param array<string, mixed> $column
     */
    #[\Override]
    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform,
    ): string {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        \assert(\is_string($value), 'Season cache value must be a string');

        /** @var SeasonResultDtoType $seasonResultData */
        $seasonResultData = \json_decode($value, true);

        return SeasonResultDto::fromCache($seasonResultData);
    }

    #[\Override]
    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        return \json_encode(
            $value,
            JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION,
        );
    }
}
