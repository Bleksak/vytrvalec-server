<?php

namespace App\Services;

use App\Entity\User;
use App\Notifications\EmailTemplate;
use App\Notifications\VytrvalecEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

final class VytrvalecMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    private function getContext(): array
    {
        return [
            'base_uri' => $this->parameterBag->get('app_base'),
        ];
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

        $template->mergeContext($this->getContext());

        $this->mailer->send(new VytrvalecEmail($emailAddresses, $template));
    }
}
