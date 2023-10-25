<?php

namespace App\Notifications;

abstract class EmailTemplate
{
    protected array $context = [];

    public function getContext(): array
    {
        return $this->context;
    }

    abstract public function getSubject(): string;
    abstract public function getTemplate(): string;
}
