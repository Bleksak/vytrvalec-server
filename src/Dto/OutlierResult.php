<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @type OutlierResultDtoType = array{user: array{firstName: string, lastName: null|string, anonymize: null|bool|int}, facultyId: int, value: int}
 */
final class OutlierResult
{
    public function __construct(
        public AnonymizedUser $user,
        public int $facultyId,
        public int $value,
    ) {}

    /**
     * @param OutlierResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        $lastName = $data['user']['lastName'] ?? null;
        $anonymize = $data['user']['anonymize'] ?? true;

        $user = new AnonymizedUser(
            $data['user']['firstName'],
            $lastName,
            $anonymize,
        );

        return new self($user, $data['facultyId'], $data['value']);
    }
}
