<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Dto\Activity\ActivityCreateTranslationDto;
use App\Dto\TranslationObjectDto;
use App\Entity\Activity;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ActivityFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $activityBikingIcon = $this->getReference('activityBikingIcon', Image::class);
        $activityRunWalkIcon = $this->getReference('activityRunWalkIcon', Image::class);

        $biking = new Activity(
            new ActivityCreateTranslationDto(
                new TranslationObjectDto(
                    cs: 'Kolo/Koloběžka/Brusle',
                    en: 'Bike/Scooter/Inline skates'
                ),
            ),
            1500,
            $activityBikingIcon,
        );

        $running = new Activity(
            new ActivityCreateTranslationDto(
                new TranslationObjectDto(
                    cs: 'Běh/Chůze',
                    en: 'Run/Walk'
                ),
            ),
            1000,
            $activityRunWalkIcon,
        );

        $manager->persist($running);
        $manager->persist($biking);
        $manager->flush();
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            ImageFixtures::class,
        ];
    }
}
