<?php

namespace App\Handlers;

use App\Entity\User;
use App\Messages\SeasonStartMessage;
use App\Notifications\EmailTemplate\SeasonStartTemplate;
use App\Notifications\VytrvalecEmail;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SeasonStartHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function __invoke(SeasonStartMessage $seasonStartMessage): void
    {
        $now = new \DateTimeImmutable();

        if ($seasonStartMessage->season->getStart()->diff($now)->days !== 0) {
            return;
        }

        $template = new SeasonStartTemplate($seasonStartMessage->season);

        $emails = array_map(
            fn (User $user) => $user->getEmail(),
            $this->userRepository->findAllForMailing()
        );

        $batchSize = 30;

        foreach (array_chunk($emails, $batchSize) as $chunk) {
            $this->mailer->send(new VytrvalecEmail($chunk, $template));
        }
    }
}
