<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ProfileCacheRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'profile-cache:fix',
    description: 'Fixes a profile cache when user has negative distance or elevation',
)]
final readonly class FixProfileCacheCommand
{
    public function __construct(
        private UserRepository $userRepository,
        private ProfileCacheRepository $profileCacheRepository,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $this->profileCacheRepository->fixCacheValues($user);
        }

        $io->success('Done');

        return Command::SUCCESS;
    }
}
