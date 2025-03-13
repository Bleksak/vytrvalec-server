<?php

namespace App\Notifications;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

final class VytrvalecEmail extends TemplatedEmail
{
    /**
     * @param string|array<string> $recipient
     */
    public function __construct(string|array $recipient, EmailTemplate $template)
    {
        parent::__construct();

        if (is_string($recipient)) {
            $recipient = [$recipient];
        }

        // TODO: use env for the mail
        $this
            ->from(new Address('vytrvale@ntis.zcu.cz', 'Měsíční Vytrvalec'))
            ->to(new Address('vytrvale@ntis.zcu.cz', 'Měsíční Vytrvalec'))
            ->bcc(...$recipient)
            ->subject($template->getSubject())
            ->htmlTemplate($template->getTemplate())
            ->context($template->getContext())
        ;
    }
}
