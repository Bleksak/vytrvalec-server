<?php

declare(strict_types=1);

namespace App\Utils\Toast;

final readonly class ToastMessage
{
    public function __construct(
        public string $type,
        public string $uuid,
        public string $message,
    ) {}
}
