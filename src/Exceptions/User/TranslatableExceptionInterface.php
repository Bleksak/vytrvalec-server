<?php

declare(strict_types=1);

namespace App\Exceptions\User;

/**
 * @api
 */
interface TranslatableExceptionInterface
{
    function toTranslatableMessage(): string;
}
