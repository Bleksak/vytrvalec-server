<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ImageFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $activityBikingIcon = new Image('/uploads/bicycle.svg');
        $activityBikingIcon->setUsedAt($activityBikingIcon->getUploadedAt());

        $activityRunWalkIcon = new Image('/uploads/person-walking.svg');
        $activityRunWalkIcon->setUsedAt($activityRunWalkIcon->getUploadedAt());

        $manager->persist($activityBikingIcon);
        $manager->persist($activityRunWalkIcon);

        $this->addReference('activityBikingIcon', $activityBikingIcon);
        $this->addReference('activityRunWalkIcon', $activityRunWalkIcon);

        $manager->flush();
    }
}
