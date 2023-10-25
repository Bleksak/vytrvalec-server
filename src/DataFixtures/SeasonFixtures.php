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
        $start = DateTimeImmutable::createFromFormat("Y-m-d", "2020-10-01");
        $end = $start->add(new DateInterval('P4W'));
        $oct2020 = new Season($start, $end, $this->getReference('anickaJirik'));

        $start = DateTimeImmutable::createFromFormat("Y-m-d", "2021-10-01");
        $end = $start->add(new DateInterval('P4W'));
        $oct2021 = new Season($start, $end, $this->getReference("davidGolias"));

        $manager->persist($oct2020);
        $manager->persist($oct2021);

        $manager->flush();
    }

    /**
    * @return array<string>
    */
    public function getDependencies(): array
    {
        return [
            CharityFixtures::class
        ];
    }
}
