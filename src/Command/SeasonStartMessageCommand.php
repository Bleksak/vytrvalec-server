<?php

declare(strict_types=1);

namespace App\Command;

use App\Messages\SeasonStartMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'season:start-message',
    description: 'Add a short description for your command',
)]
final class SeasonStartMessageCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('season_id', InputArgument::REQUIRED, 'Season ID to resend the mails')
            ->addOption('send', 's', InputOption::VALUE_NONE, 'if you really want to send the emails, you must provide this')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $send = $input->getOption('send');

        if (!$send) {
            return -1;
        }

        $seasonId = $input->getArgument('season_id');

        if (!filter_var($seasonId, FILTER_VALIDATE_INT)) {
            return -1;
        }

        if (!is_string($seasonId) && !is_int($seasonId)) {
            return -1;
        }

        $seasonId = intval($seasonId);

        $this->messageBus->dispatch(
            new SeasonStartMessage($seasonId)
        );

        return Command::SUCCESS;
    }
}
