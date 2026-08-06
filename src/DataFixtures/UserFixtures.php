<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\FacultyRepository;
use App\Utils\FeatureFlag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly FacultyRepository $facultyRepository,
    ) {}

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faculty = $this->facultyRepository->find(1);

        \assert($faculty !== null);

        $user = new User(
            email: 'existing@test.com',
            firstName: 'Jiri',
            lastName: 'Velek',
            faculty: $faculty,
            anonymize: false,
        );

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'VeryStrongPassword123@!',
        ));

        $user2 = new User(
            email: 'admin@test.com',
            firstName: 'Vytrvalec',
            lastName: 'Administrator',
            faculty: $faculty,
            anonymize: false,
            roles: [FeatureFlag::ROLE_STAFF->value],
        );

        $user2->setPassword($this->passwordHasher->hashPassword(
            $user2,
            'VeryStrongPassword123@!',
        ));

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }

    #[Override]
    public function getDependencies(): array
    {
        return [FacultyFixtures::class];
    }
}
