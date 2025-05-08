<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Messages\SeasonEndMessage;
use App\Repository\SeasonRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SeasonEndHandler
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
    ) {
    }

    public function __invoke(SeasonEndMessage $seasonEndMessage): void
    {
        $season = $this->seasonRepository->find($seasonEndMessage->seasonId);

        if ($season === null) {
            return;
        }
    }
}
