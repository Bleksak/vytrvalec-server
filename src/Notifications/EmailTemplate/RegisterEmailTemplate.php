<?php

declare(strict_types=1);

namespace App\Notifications\EmailTemplate;

use App\Notifications\AbstractEmailTemplate;

final class RegisterEmailTemplate extends AbstractEmailTemplate
{
    #[\Override]
    public function getSubject(): string
    {
        return 'Měsíční vytrvalec - registrace';
    }

    #[\Override]
    public function getTemplate(): string
    {
        return 'emails/register.twig';
    }
}
