<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Charity;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SeasonFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $start = \DateTime::createFromFormat(format: 'Y-m-d', datetime: '2020-10-01');

        if (!$start) {
            return;
        }

        $end = (clone $start)->add(new \DateInterval(duration: 'P4W'));

        $oct2020 = new Season($start, $end, $this->getReference(name: 'anickaJirik', class: Charity::class));

        $start = \DateTime::createFromFormat(format: 'Y-m-d', datetime: '2021-10-01');
        if (!$start) {
            return;
        }

        $end = (clone $start)->add(new \DateInterval(duration: 'P4W'));

        $oct2021 = new Season($start, $end, $this->getReference(name: 'davidGolias', class: Charity::class));

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
