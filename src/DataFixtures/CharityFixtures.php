<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Dto\Charity\CharityCreateTranslationDto;
use App\Dto\TranslationObjectDto;
use App\Entity\Charity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CharityFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $anickaJirik = new Charity(
            new CharityCreateTranslationDto(
                name: new TranslationObjectDto(
                    cs: 'Anička a Jiřík',
                    en: 'Anička and Jiřík'
                ),
                description: new TranslationObjectDto(
                    cs: '',
                    en: '',
                ),
            ),
        );

        $davidGolias = new Charity(
            new CharityCreateTranslationDto(
                name: new TranslationObjectDto(
                    cs: 'DAVID A GOLIÁŠ - Kubík',
                    en: 'DAVID A GOLIÁŠ - Jacob'
                ),
                description: new TranslationObjectDto(
                    cs: 'Kubík se v bříšku vyvíjel jako úplně zdravé miminko, ale při porodu se dost přidusil, což se projevilo na jeho mozečku. Diagnóza DMO. Sám nechodí, jen za ruce s dopomocí druhé osoby krátkou trasu, jinak je odkázán na vozík nebo kočárek. Je krmený mixovanou stravou a ani pití se bez další spřízněné osoby neobejde. Nemluví a je celodenně na plenách. Jinak je to moc hodný chlapec, rád spí, i když musí s dlahami na nohách,protože se mu zkracují achilovky. Má rád přiměřeně hlučnou společnost, hlasité a zejména vysoké tóny zvuků obrečí. Často se nečekaných zvuků leká. Když ho však někdo dostatečně zaujme, s chutí se i zasměje. Líbí se mu písničky, svým způsobem klácením se v židli na ně „tancuje“. Má svůj svět, miluje kropáček, dokáže desítky minut nabírat do kropáčku vodu, vylívat a druhou rukou chytat proud vody. Navštěvuje stacionář Človíček, kde je moc spokojený. Jezdí rád na hipoterapii, kde je úplně nadšený.',
                    en: 'Jacob developed in the womb as a completely healthy baby, but during birth he suffered from a lack of oxygen, which affected his cerebellum. Diagnosis: cerebral palsy. He cannot walk on his own, only short distances while holding hands with the help of another person; otherwise, he relies on a wheelchair or stroller. He is fed blended food and also needs assistance with drinking. He does not speak and wears diapers all day. Otherwise, he is a very sweet boy. He likes to sleep, even though he has to wear leg splints because his Achilles tendons are shortening. He enjoys moderately lively company, but cries at loud and especially high-pitched sounds. He often gets startled by unexpected noises. However, when something captures his attention, he laughs with joy. He likes songs and, in his own way, “dances” to them by swaying in his chair. He has his own little world—he loves playing with a watering can, spending long minutes filling it with water, pouring it out, and catching the stream of water with his other hand. He attends the day center Človíček, where he is very happy. He also enjoys hippotherapy, which excites him tremendously.',
                ),
            ),
        );

        $manager->persist($anickaJirik);
        $manager->persist($davidGolias);

        $this->addReference('anickaJirik', $anickaJirik);
        $this->addReference('davidGolias', $davidGolias);

        $manager->flush();
    }
}
