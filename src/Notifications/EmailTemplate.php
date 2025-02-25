<?php

namespace App\Notifications;

abstract class EmailTemplate
{
    protected array $context = [];

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(string $name, mixed $value): void
    {
        $this->context[$name] = $value;
    }

    abstract public function getSubject(): string;

    abstract public function getTemplate(): string;
}
