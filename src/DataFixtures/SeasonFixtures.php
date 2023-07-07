<?php

namespace App\DataFixtures;

use App\Entity\Season;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $oct2020 = new Season();
        $start = DateTimeImmutable::createFromFormat("Y-m-d", "2020-10-01");
        $end = $start->add(new DateInterval('P4W'));
        $oct2020->setStart($start);
        $oct2020->setEnd($end);
        $oct2020->setCharity($this->getReference("anickaJirik"));

        $oct2021 = new Season();
        $start = DateTimeImmutable::createFromFormat("Y-m-d", "2021-10-01");
        $end = $start->add(new DateInterval('P4W'));
        $oct2021->setStart($start);
        $oct2021->setEnd($end);
        $oct2021->setCharity($this->getReference("davidGolias"));

        $manager->persist($oct2020);
        $manager->persist($oct2021);

        $manager->flush();
    }

    public function getDependencies() {
        return [
            CharityFixtures::class
        ];
    }
}
