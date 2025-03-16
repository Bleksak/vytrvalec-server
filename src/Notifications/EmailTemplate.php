<?php

namespace App\Notifications;

abstract class EmailTemplate
{
    /**
     * @var array<string, mixed>
     */
    protected array $context = [];

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

    public function __construct(string $appUrl)
    {
        $this->context['app_url'] = $appUrl;
    }

    abstract public function getSubject(): string;

    abstract public function getTemplate(): string;
}
