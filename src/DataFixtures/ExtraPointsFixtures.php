<?php

namespace App\DataFixtures;

use App\Entity\ExtraPoints;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExtraPointsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $dailyDistanceExtraPoints = new ExtraPoints();
        $dailyDistanceExtraPoints->setName('daily_distance');
        $dailyDistanceExtraPoints->setPoints(1);
        $dailyDistanceExtraPoints->setWeek(2);

        $manager->persist($dailyDistanceExtraPoints);

        $weeklyDistanceExtraPoints = new ExtraPoints();
        $weeklyDistanceExtraPoints->setName('weekly_distance');
        $weeklyDistanceExtraPoints->setPoints(1);
        $weeklyDistanceExtraPoints->setWeek(2);

        $manager->persist($weeklyDistanceExtraPoints);

        $weeklyElevationExtraPoints = new ExtraPoints();
        $weeklyElevationExtraPoints->setName('weekly_elevation');
        $weeklyElevationExtraPoints->setPoints(2);
        $weeklyElevationExtraPoints->setWeek(3);

        $manager->persist($weeklyElevationExtraPoints);

        $manager->flush();
    }
}