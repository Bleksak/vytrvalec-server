<?php

declare(strict_types=1);

namespace App\Notifications;

abstract class EmailTemplate
{
    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(string $name, mixed $value): void
    {
        $this->context[$name] = $value;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function mergeContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }

    abstract public function getSubject(): string;

    abstract public function getTemplate(): string;
}
