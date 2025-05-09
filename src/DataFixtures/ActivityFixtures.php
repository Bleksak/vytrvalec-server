<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ActivityFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $biking = new Activity('Kolo/Koloběžka', 1500);
        $running = new Activity('Běh/Chůze', 1000);

        $manager->persist($running);
        $manager->persist($biking);
        $manager->flush();
    }
}
