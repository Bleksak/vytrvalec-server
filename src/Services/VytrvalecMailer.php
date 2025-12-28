<?php

declare(strict_types=1);

namespace App\Services;

use App\Controller\EmailUnsubscribeController;
use App\Entity\User;
use App\Notifications\AbstractEmailTemplate;
use App\Notifications\VytrvalecEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class VytrvalecMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGeneratorInterface,
        private string $appBase,
    ) {}

    /**
     * @return array<string, mixed>
     */
    private function getContext(): array
    {
        return [
            'base_uri' => $this->appBase,
        ];
    }

    private function constructUnsubscribeLink(User $user): string
    {
        $emailUnsubscribeHash = $user->getEmailUnsubscribeHash();

        return $this->urlGeneratorInterface->generate(EmailUnsubscribeController::ROUTE, [
            'emailUnsubscribeHash' => $emailUnsubscribeHash,
        ]);
    }

    /**
     * @param User|array<User> $recipient
     */
    public function send(
        User|array $recipient,
        AbstractEmailTemplate $template,
        bool $forceSend = false,
    ): void {
        if (!\is_array($recipient)) {
            /** @var array<User> $recipient */
            $recipient = [$recipient];
        }

        $template->mergeContext($this->getContext());

        foreach ($recipient as $user) {
            $email = $user->email;

            if ($email === null) {
                continue;
            }

            if ($user->hasMailing() || $forceSend) {
                $template->setContext(
                    'unsubscribe_link',
                    $this->constructUnsubscribeLink($user),
                );

                $mail = new VytrvalecEmail($email, $template);
                if ($template->replyTo) {
                    $mail->replyTo($template->replyTo);
                }

                $this->mailer->send($mail);
            }
        }
    }
}
