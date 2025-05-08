<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Charity;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SeasonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $start = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-10-01');

        if (!$start) {
            return;
        }

        $end = $start->add(new \DateInterval('P4W'));

        $oct2020 = new Season($start, $end, $this->getReference('anickaJirik', Charity::class));

        $start = \DateTimeImmutable::createFromFormat('Y-m-d', '2021-10-01');
        if (!$start) {
            return;
        }

        $end = $start->add(new \DateInterval('P4W'));

        $oct2021 = new Season($start, $end, $this->getReference('davidGolias', Charity::class));

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
            CharityFixtures::class,
        ];
    }
}
