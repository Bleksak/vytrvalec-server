<?php

namespace App\DataFixtures;

use App\Entity\Faculty;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FacultyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $fav = new Faculty();
        $fav->setName("Fakulta aplikovaných věd");
        $fav->setShortcut("FAV");
        $fav->setVisible(true);

        $fdu = new Faculty();
        $fdu->setName("Fakulta designu a umění Ladislava Sutnara");
        $fdu->setShortcut("FDU");
        $fdu->setVisible(true);

        $fek = new Faculty();
        $fek->setName("Fakulta ekonomická");
        $fek->setShortcut("FEK");
        $fek->setVisible(true);

        $fel = new Faculty();
        $fel->setName("Fakulta elektrotechnická");
        $fel->setShortcut("FEL");
        $fel->setVisible(true);

        $ff = new Faculty();
        $ff->setName("Fakulta filozofická");
        $ff->setShortcut("FF");
        $ff->setVisible(true);

        $fpe = new Faculty();
        $fpe->setName("Fakulta pedagogická");
        $fpe->setShortcut("FPE");
        $fpe->setVisible(true);

        $fpr = new Faculty();
        $fpr->setName("Fakulta právnická");
        $fpr->setShortcut("FPR");
        $fpr->setVisible(true);

        $fst = new Faculty();
        $fst->setName("Fakulta strojní");
        $fst->setShortcut("FST");
        $fst->setVisible(true);

        $fzs = new Faculty();
        $fzs->setName("Fakulta zdravotních studií");
        $fzs->setShortcut("FZS");
        $fzs->setVisible(true);

        $manager->persist($fav);
        $manager->persist($fdu);
        $manager->persist($fek);
        $manager->persist($fel);
        $manager->persist($ff);
        $manager->persist($fpe);
        $manager->persist($fpr);
        $manager->persist($fst);
        $manager->persist($fzs);
        
        $manager->flush();
    }
}
