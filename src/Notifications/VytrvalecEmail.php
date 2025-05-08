<?php

declare(strict_types=1);

namespace App\Notifications;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

final class VytrvalecEmail extends TemplatedEmail
{
    public function __construct(string $recipient, EmailTemplate $template)
    {
        parent::__construct();

        // TODO: use env for the mail
        $this
            ->from(new Address('vytrvale@ntis.zcu.cz', 'Měsíční Vytrvalec'))
            ->to(new Address($recipient))
            // ->bcc('vytrvale@ntis.zcu.cz')
            ->subject($template->getSubject())
            ->htmlTemplate($template->getTemplate())
            ->context($template->getContext())
        ;
    }
}
