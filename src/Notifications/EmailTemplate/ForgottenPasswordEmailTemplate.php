<?php

declare(strict_types=1);

namespace App\Notifications\EmailTemplate;

use App\Notifications\AbstractEmailTemplate;

final class ForgottenPasswordEmailTemplate extends AbstractEmailTemplate
{
    #[\Override]
    public function getSubject(): string
    {
        return 'Měsíční vytrvalec - zapomenuté heslo';
    }

    #[\Override]
    public function getTemplate(): string
    {
        return 'emails/forgotten_password.twig';
    }
}
