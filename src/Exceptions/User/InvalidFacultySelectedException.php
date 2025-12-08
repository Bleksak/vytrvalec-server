<?php

declare(strict_types=1);

namespace App\Exceptions\User;

use RuntimeException;

final class InvalidFacultySelectedException extends RuntimeException implements
    TranslatableExceptionInterface
{
    /**
     * @return array<string, mixed>
     */
    public function clientSideError(): array
    {
        return [
            'faculty' => ['invalid'],
        ];
    }

    #[\Override]
    public function toTranslatableMessage(): string
    {
        return 'user.faculty.invalid';
    }
}
