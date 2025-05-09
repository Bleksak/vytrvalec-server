<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Notifications\AbstractEmailTemplate;
use App\Notifications\VytrvalecEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class VytrvalecMailer
{
    private string $clientUrl;

    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag,
    ) {
        $this->clientUrl = $parameterBag->get('client_url');
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

    private function constructUnsubscribeLink(User $user): string
    {
        return sprintf('%s/unsubscribe/%s', $this->clientUrl, $user->getEmailUnsubscribeHash());
    }

    /**
     * @param User|array<User> $recipient
     */
    public function send(User|array $recipient, AbstractEmailTemplate $template, bool $forceSend = false): void
    {
        if (!is_array($recipient)) {
            /** @var array<User> $recipient */
            $recipient = [$recipient];
        }

        $template->mergeContext($this->getContext());

        foreach ($recipient as $user) {
            $email = $user->getEmail();

            if ($email === null) {
                continue;
            }

            if ($user->hasMailing() || $forceSend) {
                $template->setContext('unsubscribe_link', $this->constructUnsubscribeLink($user));

                $this->mailer->send(new VytrvalecEmail($email, $template));
            }
        }
    }
}
