<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Dto\Faculty\FacultyCreateTranslationDto;
use App\Dto\TranslationObjectDto;
use App\Entity\Faculty;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class FacultyFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faculties = [
            new Faculty(
                new FacultyCreateTranslationDto(
                    name : new TranslationObjectDto(
                        cs: 'Fakulta aplikovaných věd',
                        en: 'Faculty of Applied Sciences'
                    ),
                ),
                shortcut: 'FAV',
                visible: true,
                color: '#DBAC00'
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta designu a umění Ladislava Sutnara',
                        en: 'Ladislav Sutnar Faculty of Design and Art',
                    ),
                ),
                shortcut: 'FDU',
                visible: true,
                color: '#E63329',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta ekonomická',
                        en: 'Faculty of Economics',
                    ),
                ),
                shortcut: 'FEK',
                visible: true,
                color: '#EE7202',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta elektrotechnická',
                        en: 'Faculty of Electrical Engineering',
                    ),
                ),
                shortcut: 'FEL',
                visible: true,
                color: '#064291',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta filozofická',
                        en: 'Faculty of Arts',
                    ),
                ),
                shortcut: 'FF',
                visible: true,
                color: '#00B5DD',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta pedagogická',
                        en: 'Faculty of Education',
                    ),
                ),
                shortcut: 'FPE',
                visible: true,
                color: '#8ABD24',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta právnická',
                        en: 'Faculty of Law',
                    ),
                ),
                shortcut: 'FPR',
                visible: true,
                color: '#A20E2A',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta strojní',
                        en: 'Faculty of Mechanical Engineering',
                    ),
                ),
                shortcut: 'FST',
                visible: true,
                color: '#008FD0',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(
                    name: new TranslationObjectDto(
                        cs: 'Fakulta zdravotních studií',
                        en: 'Faculty of Health Care Studies',
                    ),
                ),
                shortcut: 'FZS',
                visible: true,
                color: '#009767'
            ),
        ];

        foreach ($faculties as $faculty) {
            $manager->persist($faculty);
        }

        $manager->flush();
    }
}
