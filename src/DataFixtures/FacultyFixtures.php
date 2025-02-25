<?php

namespace App\DataFixtures;

use App\Entity\Faculty;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FacultyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faculties = [
            new Faculty('Fakulta aplikovaných věd', 'FAV', true),
            new Faculty('Fakulta designu a umění Ladislava Sutnara', 'FDU', true),
            new Faculty('Fakulta ekonomická', 'FEK', true),
            new Faculty('Fakulta elektrotechnická', 'FEL', true),
            new Faculty('Fakulta filozofická', 'FF', true),
            new Faculty('Fakulta pedagogická', 'FPE', true),
            new Faculty('Fakulta právnická', 'FPR', true),
            new Faculty('Fakulta strojní', 'FST', true),
            new Faculty('Fakulta zdravotních studií', 'FZS', true),
        ];

        foreach ($faculties as $faculty) {
            $manager->persist($faculty);
        }

        $manager->flush();
    }
}
