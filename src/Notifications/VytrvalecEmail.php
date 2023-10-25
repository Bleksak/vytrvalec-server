<?php

namespace App\Notifications;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class VytrvalecEmail extends TemplatedEmail
{
    public function __construct(User $recipient, EmailTemplate $template)
    {
        parent::__construct();

        // TODO: use env for the mail
        $this
            ->from(new Address('vytrvale@ntis.zcu.cz'), 'Měsíční vytrvalec')
            ->to($recipient->getEmail())
            ->subject($template->getSubject())
            ->htmlTemplate($template->getTemplate())
            ->context($template->getContext())
        ;
    }
}
