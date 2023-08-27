<?php

namespace App\Recipient;

use Symfony\Component\Notifier\Recipient\RecipientInterface;

class ExpoRecipient implements RecipientInterface
{
    private string $expoToken;
    public function __construct(string $expoToken)
    {
        $this->expoToken = $expoToken;
    }

    public function expo(string $expoToken): static
    {
        $this->expoToken = $expoToken;
        return $this;
    }

    public function getExpo(): string
    {
        return $this->expoToken;
    }
}