<?php

namespace App\DataFixtures;

use App\Entity\ExtraPoints;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ExtraPointsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dailyDistanceExtraPoints = new ExtraPoints('daily_distance', 1, 2);

        $manager->persist($dailyDistanceExtraPoints);

        $weeklyDistanceExtraPoints = new ExtraPoints('weekly_distance', 1, 2);

        $manager->persist($weeklyDistanceExtraPoints);

        $weeklyElevationExtraPoints = new ExtraPoints('weekly_elevation', 2, 3);

        $manager->persist($weeklyElevationExtraPoints);

        $manager->flush();
    }
}
