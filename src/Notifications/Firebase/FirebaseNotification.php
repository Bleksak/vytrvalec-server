<?php

namespace App\Notifications\Firebase;

abstract class FirebaseNotification
{
    public function __construct(private readonly string $to, private readonly string $title, private readonly string $message, private readonly ?string $action = null)
    {
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
