<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActivityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $biking = new Activity();
        $biking->setActive(true);
        $biking->setName("Kolo/Koloběžka");
        $biking->setMinElevation(1500);

        $running = new Activity();
        $running->setActive(true);
        $running->setName("Běh/Chůze");
        $running->setMinElevation(1000);

        $manager->persist($running);
        $manager->persist($biking);
        $manager->flush();
    }
}
