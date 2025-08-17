<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ProfileCacheRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\Argument;
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

    public function __invoke(SymfonyStyle $io, #[Argument] int $userId): int
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            $io->error('User not found');

            return Command::FAILURE;
        }

        $this->profileCacheRepository->fixCacheValues($user);

        return Command::SUCCESS;
    }
}
