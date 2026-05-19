<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Dto\Activity\ActivityCreateTranslationDto;
use App\Dto\TranslationObjectDto;
use App\Entity\Activity;
use App\Entity\Image;
use App\Utils\MimeType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;

final class ActivityFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $runWalkIcon = new Image(
            '/uploads/activity-run-walk.svg',
            MimeType::SVG,
        );

        $bikeScooterIcon = new Image(
            '/uploads/activity-bike-scooter.svg',
            MimeType::SVG,
        );

        $manager->persist($runWalkIcon);
        $manager->persist($bikeScooterIcon);

        $activities = [
            new Activity(
                new ActivityCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Běh a chůze',
                    en: 'Run and Walk',
                )),
                1000,
                $runWalkIcon,
            ),
            new Activity(
                new ActivityCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Kolo, koloběžka',
                    en: 'Bicycle & Scooter',
                )),
                1500,
                $bikeScooterIcon,
            ),
        ];

        foreach ($activities as $activity) {
            $manager->persist($activity);
        }

        $manager->flush();
    }
}
