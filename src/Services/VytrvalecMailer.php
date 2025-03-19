<?php

namespace App\Services;

use App\Entity\User;
use App\Notifications\EmailTemplate;
use App\Notifications\VytrvalecEmail;
use Symfony\Component\Mailer\MailerInterface;

final class VytrvalecMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @param User|array<User> $recipient
     */
    public function send(User|array $recipient, EmailTemplate $template, bool $forceSend = false): void
    {
        if (!is_array($recipient)) {
            /** @var array<User> $recipient */
            $recipient = [$recipient];
        }

        $emailAddresses = [];

        foreach ($recipient as $user) {
            if ($user->hasMailing() && !$forceSend) {
                $emailAddresses[] = $user->getEmail();
            }
        }

        $this->mailer->send(new VytrvalecEmail($emailAddresses, $template));
    }
}
