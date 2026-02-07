<?php

declare(strict_types=1);

namespace App\Notifications\Firebase;

/**
 * @api
 */
abstract readonly class AbstractFirebaseNotification
{
    /**
     * @param non-empty-string $to
     */
    public function __construct(
        public string $to,
        public string $title,
        public string $message,
        public ?string $action = null,
    ) {}
}
