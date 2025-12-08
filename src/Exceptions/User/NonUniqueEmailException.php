<?php

declare(strict_types=1);

namespace App\Exceptions\User;

use RuntimeException;

final class NonUniqueEmailException extends RuntimeException implements
    TranslatableExceptionInterface
{
    /**
     * @return array<string, mixed>
     */
    public function clientSideError(): array
    {
        return [
            'email' => ['not_unique'],
        ];
    }

    #[\Override]
    public function toTranslatableMessage(): string
    {
        return 'user.email.not_unique';
    }
}
