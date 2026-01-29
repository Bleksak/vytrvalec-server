<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @import-type AnonymizedUserDtoType from AnonymizedUser
 * @type OutlierResultDtoType = array{user: int|AnonymizedUserDtoType, facultyId: int, value: int}
 */
final class OutlierResult
{
    public function __construct(
        public int|AnonymizedUser $user,
        public int $facultyId,
        public int $value,
    ) {}

    /**
     * @param OutlierResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        $user = \is_int($data['user'])
            ? $data['user']
            : AnonymizedUser::fromArray($data['user']);

        return new self($user, $data['facultyId'], $data['value']);
    }

    public function toArray(): array
    {
        return [
            'user' => \is_int($this->user)
                ? $this->user
                : $this->user->toArray(),
            'facultyId' => $this->facultyId,
            'value' => $this->value,
        ];
    }
}
