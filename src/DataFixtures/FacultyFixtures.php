<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Dto\Faculty\FacultyCreateTranslationDto;
use App\Dto\TranslationObjectDto;
use App\Entity\Faculty;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;

final class FacultyFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faculties = [
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta Aplikovaných Věd',
                    en: 'Faculty of Applied Sciences',
                )),
                'FAV',
                true,
                '#e0b100',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta designu a umění Ladislava Sutnara',
                    en: 'Ladislav Sutnar Faculty of Design and Art',
                )),
                'FDU',
                true,
                '#e5344c',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta ekonomická',
                    en: 'Faculty of Economics',
                )),
                'FEK',
                true,
                '#eb6e08',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta elektrotechnická',
                    en: 'Faculty of Electrical Engineering',
                )),
                'FEL',
                true,
                '#074391',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta filozofická',
                    en: 'Faculty of Arts',
                )),
                'FF',
                true,
                '#00b6d7',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta pedagogická',
                    en: 'Faculty of Education',
                )),
                'FPE',
                true,
                '#8fbe22',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta právnická',
                    en: 'Faculty of Law',
                )),
                'FPR',
                true,
                '#600128',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta strojní',
                    en: 'Faculty of Mechanical Engineering',
                )),
                'FST',
                true,
                '#3889ba',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Fakulta zdravotnických studií',
                    en: 'Faculty of Healthcare',
                )),
                'FZS',
                true,
                '#006b65',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Rektorát',
                    en: 'Rectorate',
                )),
                'REK',
                true,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Univerzita třetího věku',
                    en: 'University of the Third Age',
                )),
                'U3V',
                true,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Ústav jazykové přípravy',
                    en: 'Institute of Applied Language Studies',
                )),
                'UJP',
                true,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Nové technologie - Výzkumné centrum',
                    en: 'New Technologies - Research Center',
                )),
                'NTC',
                true,
                '#940084',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Univerzitní knihovna',
                    en: 'University Library',
                )),
                'KNIHOVNA',
                true,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Centrum informatizace a výpočetní techniky',
                    en: 'Information Technology Center',
                )),
                'CIV',
                true,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Školicí a ubytovací zařízení Nečtiny',
                    en: 'Training and Accommodation Facility Nečtiny',
                )),
                'SUZN',
                false,
                '#005cab',
            ),
            new Faculty(
                new FacultyCreateTranslationDto(new TranslationObjectDto(
                    cs: 'Ústav tělesné výchovy a sportu',
                    en: 'Institute of Physical Education and Sport',
                )),
                'UTS',
                true,
                '#005cab',
            ),
        ];

        foreach ($faculties as $faculty) {
            $manager->persist($faculty);
        }

        $manager->flush();
    }
}
