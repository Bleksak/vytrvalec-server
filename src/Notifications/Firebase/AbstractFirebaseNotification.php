<?php

declare(strict_types=1);

namespace App\Notifications\Firebase;

abstract readonly class AbstractFirebaseNotification
{
    public function __construct(
        private string $to,
        private string $title,
        private string $message,
        private ?string $action = null,
    ) {
    }

    public function to(): string
    {
        return $this->to;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function action(): ?string
    {
        return $this->action;
    }
}
