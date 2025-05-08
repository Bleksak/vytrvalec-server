<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ProfileCacheRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'profile-cache:fix',
    description: 'Fixes a profile cache when user has negative distance or elevation',
)]
final class FixProfileCacheCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ProfileCacheRepository $profileCacheRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::REQUIRED, 'User id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userId = $input->getArgument('user');

        $user = $this->userRepository->find($userId);
        if (!$user) {
            $io->error('User not found');

            return Command::FAILURE;
        }

        $this->profileCacheRepository->fixCacheValues($user);

        return Command::SUCCESS;
    }
}
