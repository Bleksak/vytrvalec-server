<?php

namespace App\DataFixtures;

use App\Entity\Charity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CharityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $anickaJirik = new Charity();
        $anickaJirik->setName("Anička a Jiřík");
        $anickaJirik->setDescription("");

        $davidGolias = new Charity();
        $davidGolias->setName("DAVID a GOLIÁŠ - Kubík");
        $davidGolias->setDescription("Kubík – kombinované postižení- 11let. Kubík se v bříšku vyvíjel jako úplně zdravé miminko, ale při porodu se dost přidusil, což se projevilo na jeho mozečku. Diagnóza DMO.");

        $manager->persist($anickaJirik);
        $manager->persist($davidGolias);

        $this->addReference("anickaJirik", $anickaJirik);
        $this->addReference("davidGolias", $davidGolias);

        $manager->flush();
    }
}
