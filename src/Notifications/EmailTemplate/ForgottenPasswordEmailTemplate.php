<?php

namespace App\Notifications\EmailTemplate;

use App\Notifications\EmailTemplate;

class ForgottenPasswordEmailTemplate extends EmailTemplate
{
    public function getSubject(): string
    {
        return 'Měsíční vytrvalec - zapomenuté heslo';
    }

    public function getTemplate(): string
    {
        return 'emails/forgotten_password.twig';
    }
}
