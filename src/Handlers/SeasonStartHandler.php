<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Messages\SeasonStartMessage;
use App\Notifications\EmailTemplate\SeasonStartTemplate;
use App\Repository\SeasonRepository;
use App\Repository\UserRepository;
use App\Services\VytrvalecMailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SeasonStartHandler
{
    public function __construct(
        private SeasonRepository $seasonRepository,
        private UserRepository $userRepository,
        private VytrvalecMailer $mailer,
    ) {}

    public function __invoke(SeasonStartMessage $seasonStartMessage): void
    {
        $season = $this->seasonRepository->find($seasonStartMessage->seasonId);

        if ($season === null) {
            return;
        }

        $now = new \DateTimeImmutable();

        if ($season->getStart()->diff($now)->days !== 0) {
            return;
        }

        $template = new SeasonStartTemplate($season);

        $users = $this->userRepository->findAllForMailing();

        $batchSize = 30;

        foreach (array_chunk($users, $batchSize) as $chunk) {
            $this->mailer->send($chunk, $template);
        }
    }
}
