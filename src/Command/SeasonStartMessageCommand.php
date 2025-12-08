<?php

declare(strict_types=1);

namespace App\Command;

use App\Messages\SeasonStartMessage;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'season:start-message',
    description: 'Resends e-mails for the given season',
)]
final readonly class SeasonStartMessageCommand
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument] int $seasonId,
        #[Option] bool $send,
    ): int {
        if (!$send) {
            $io->writeln('Send option is required for emails to be sent!');
            return Command::FAILURE;
        }

        $this->messageBus->dispatch(new SeasonStartMessage($seasonId));

        return Command::SUCCESS;
    }
}
