<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @type OutlierResultDtoType = array{user: int, facultyId: int, value: int}
 */
final class OutlierResult
{
    public function __construct(
        public int $user,
        public int $facultyId,
        public int $value,
    ) {}

    /**
     * @param OutlierResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        return new self($data['user'], $data['facultyId'], $data['value']);
    }

    /**
     * @return OutlierResultDtoType
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'facultyId' => $this->facultyId,
            'value' => $this->value,
        ];
    }
}
