<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ClientUrlBuilderFactory
{
    public function __construct(
        private string $clientUrl,
    ) {}

    public function builder(): UrlBuilder
    {
        return new UrlBuilder($this->clientUrl);
    }
}
