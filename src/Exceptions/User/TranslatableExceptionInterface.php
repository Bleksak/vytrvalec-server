<?php

declare(strict_types=1);

namespace App\Exceptions\User;

interface TranslatableExceptionInterface
{
    function toTranslatableMessage(): string;
}
