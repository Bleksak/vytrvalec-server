<?php

namespace App\Notifications\EmailTemplate;

use App\Notifications\EmailTemplate;

class RegisterEmailTemplate extends EmailTemplate
{
    public function getSubject(): string
    {
        return 'Měsíční vytrvalec - registrace';
    }

    public function getTemplate(): string
    {
        return 'emails/register.twig';
    }
}
