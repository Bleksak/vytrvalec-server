<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActivityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $biking = new Activity('Kolo/Koloběžka', 1500);
        $running = new Activity('Běh/Chůze', 1000);

        $manager->persist($running);
        $manager->persist($biking);
        $manager->flush();
    }
}
