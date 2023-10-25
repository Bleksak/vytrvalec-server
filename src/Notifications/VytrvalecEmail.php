<?php

namespace App\Notifications;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class VytrvalecEmail extends TemplatedEmail
{
    public function __construct(User $recipient, string $subject, string $template, $data = [])
    {
        parent::__construct();

        // TODO: use env for this
        $this
            ->from(new Address('vytrvale@ntis.zcu.cz'), 'Měsíční vytrvalec')
            ->to($recipient->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($data)
        ;
    }
}
