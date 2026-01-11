<?php

declare(strict_types=1);

return [
    'navbar' => [
        'administration' => 'Administrace',
        'rules' => 'Pravidla',
        'results' => 'Výsledky',
        'profile' => 'Profil',
        'register' => 'Registrace',
        'login' => 'Přihlásit',
        'logout' => 'Odhlásit',
        'submission' => 'Nahrát aktivitu',
    ],
    'rules' => [
        'title' => 'Pravidla výzvy',
        'intro' => [
            'content1' => 'Běhejte, choďte jezděte na kole, koloběžce, bruslích! Nejen, že uděláte něco pro svoje zdraví, ale přispějete na dobrou věc! Kilometry, které soutěžící urazí se přemění na koruny a přispějeme tím na charitu.',
            'content2' => 'Průběžný počet najetých a uběhnutých kilometrů fakult a mimofakultních pracovišť budeme vyhodnocovat po týdnu a každá fakulta či pracoviště mají možnost získat body do celkového hodnocení.',
        ],
        'disciplines' => [
            'title' => 'Soutěž probíhá ve dvou disciplínách',
        ],
        'activities' => [
            'run_walk' => 'Běh a chůze',
            'bicycle' => 'Kolo, koloběžka a brusle',
        ],
        'progress' => [
            'title' => 'Průběh soutěže',
            'content1' => 'Uživatelé zaznamenávají své cílené pohybové aktivity prostřednictvím některé mobilní aplikace na svůj telefon a nahrají je na tento portál. Pro zaznamenávání cílových aktivit můžete využít např. aplikaci Garmin Connect, nebo Strava.',
            'content2' => 'Každý týden se vyhodnocuje počet uražených kilometrů v daných disciplínách. První tým dostane v každé disciplíně např. 15 bodů (podle počtu zůčastněných fakult a pracovišť), druhý 14 bodu, třetí 13 bodů, apod. Celkovým vítězem výzvy se stane tým, který získá nejvíce bodů za 4 týdny v součtu obou disciplín.',
        ],
        'target_activities' => [
            'title' => 'Cílová pohybová aktivita',
            'body' => 'Jdu si zaběhat, zapnu si aktivitu a vypnu si aktivitu. Jdu se projít, zapnu si aktivitu a vypnu si aktivitu. Jedu na kole, zapnu si aktivitu a vypnu si aktivitu.',
            'supplement' => 'Cílová pohybová aktivita NENÍ součet nachozených kilometrů za den. Pro zaznamenávání cílových aktivit můžete využít např. aplikaci Garmin Connect, nebo Strava.',
        ],
        'extra_points' => [
            'title' => 'Extra body!',
            'intro' => 'Jednotlivci mohou pro svou fakultu získat extra body.',
            'extra_point' => 'EXTRA BOD',
            'extra_points' => 'EXTRA BODY',
            'third_week' => [
                'title' => 'Třetí týden',
                'extra_one' => 'pro jednotlivce, který uběhne/ujde/ujede nejvíce kilometrů v jednom dni v dané disciplíně.',
                'extra_two' => 'pro jednotlivce s největším součtem kilometrů za celý týden v dané disciplíně.',
            ],
            'fourth_week' => [
                'title' => 'Čtvrtý týden',
                'extra_one' => 'pro jednotlivce s největším převýšením v jedné aktivitě v dané disciplíně. Chůze více než 1000m a jízda více než 1500m.',
            ],
        ],
    ],
    'login' => [
        'title' => 'Přihlášení',
        'email' => 'E-mail:',
        'password' => 'Heslo:',
        'submit' => 'Přihlásit',
        'user_not_found' => 'Neplatný e-mail nebo heslo',
        'success' => 'Přihlášení proběhlo úspěšně',
    ],
    'registration' => [
        'title' => 'Registrace',
        'faculty' => 'Fakulta:',
        'email' => 'E-mail:',
        'password' => 'Heslo:',
        'password_repeat' => 'Heslo znovu:',
        'first_name' => 'Křestní jméno:',
        'last_name' => 'Příjmení:',
        'anonymize_tooltip' => 'Aplikace zveřejňuje jména účastníků, kteří získali bonusové body pro svou fakultu nebo dosáhli mimořádných výsledků. Pokud souhlas neudělíte, vaše jméno nebude zveřejněno a zobrazí se pouze název fakulty. Svůj souhlas můžete kdykoli změnit v nastavení profilu.',
        'anonymize' => 'Souhlasím se zveřejněním celého jména ve výsledcích',
        'gdpr_tooltip' => 'Jméno, příjmení a emailová adresa jsou zpracovávány pouze pro nezbytné fungování aplikace a nejsou sdíleny s žádnou třetí stranou',
        'gdpr' => 'Souhlasím se zpracováním osobních údajů',
        'submit' => 'Registrovat',
        'success' => 'Registrace proběhla úspěšně',
    ],
    'home' => [
        'intro' => 'Akce měsíční vytrvalec vznikla během zimního semestru v roce 2020. Tuto pohybovou soutěž připravil a zorganizoval Ústav tělesné výchovy a sportu ZČU s úmyslem rozhýbat studenty během distanční výuky, která probíhala na ZČU během koronavirové pandemie.',
        'about' => [
            'title' => 'O Výzvě',
            'content' => 'Účastníci bojují čtyři týdny za svoje týmy (fakulta, VŠ ústav / rektorátní pracoviště / U3V) v počtu naběhaných a naježděných kilometrů. Ty se v rámci týmů sčítají a v závěru týdne určují počet bodů, které daný tým získá a podle toho se umístí v celkovém pořadí.',
        ],
        'statistics' => [
            'title' => 'Statistiky',
            'users' => 'Účastníků',
        ],
    ],
    'footer' => [
        'uts' => 'Ústav tělesné výchovy a sportu',
        'managed_by' => 'Správa webu',
    ],
    'season_detail' => [
        'title' => 'Detail Sezóny',
        'results' => 'Výsledky Sezóny',
        'date_range' => '{start} - {end}',
        'finish_place' => '{place, select,
            1 {První místo: {name}}
            2 {Druhé místo: {name}}
            3 {Třetí místo: {name}}
            other {{place}. {name}}
        }',
        'charity' => [
            'no_image' => 'Charita nemá obrázek',
            'visit' => 'Navštivte web charity',
            'raised' => 'Vybráno {money} Kč',
        ],
    ],
    'user' => [
        'login' => [
            'not_found' => 'E-mail nebo heslo jsou nesprávné',
            'success' => 'Přihlášení proběhlo úspěšně',
        ],
        'faculty' => [
            'invalid' => 'Byla zvolena neplatná fakulta',
        ],
        'email' => [
            'not_unique' => 'Uživatel se zadanou e-mailovou adresou již existuje',
        ],
        'forgotten_password' => [
            'prompt' => 'Zapomenuté heslo?',
            'title' => 'Zapomenuté heslo',
            'description' => 'Na e-mailovou adresu bude zaslán odkaz k obnovení hesla.',
            'email' => 'E-mail:',
            'submit' => 'Odeslat',
            'password' => 'Nové heslo:',
            'password_repeat' => 'Nové heslo znovu:',
            'change' => 'Změnit heslo',
        ],
        'password_reset' => [
            'success' => 'Heslo bylo úspěšně změněno',
        ],
    ],
    'results' => [
        'title' => 'Výsledky | Měsíční Vytrvalec',
        'week_picker' => [
            'whole_season' => 'Celá sezóna',
            'first_week' => 'První týden',
            'second_week' => 'Druhý týden',
            'third_week' => 'Třetí týden',
            'fourth_week' => 'Čtvrtý týden',
        ],
        'user_count' => 'Počet účastníků',
        'user_count_by_faculty' => 'Účastníci podle fakult',
        'faculty' => 'Fakulta',
        'distance' => 'Vzdálenost',
        'points' => 'Počet bodů',
        'total' => 'Celkem',
        'extras' => [
            'title' => 'Extra body',
            'name' => 'Jméno',
            'faculty' => 'Fakulta',
            'category' => 'Kategorie',
            'activity' => 'Aktivita',
            'value' => 'Výkon',
            'points' => 'Body',
            'daily_distance' => 'Vzdálenost za den',
            'weekly_distance' => 'Vzdálenost za týden',
            'weekly_elevation' => 'Převýšení za týden',
        ],
        'outliers' => [
            'title' => 'TOP účastníci',
            'name' => 'Jméno',
        ],
    ],
    'profile' => [
        'title' => 'Profil | Měsíční Vytrvalec',
        'header' => 'Profil',
        'no_submissions' => 'Profil nemá zatím žádnou aktivitu',
    ],
    'submission' => [
        'state' => [
            'accepted' => 'Schváleno',
            'rejected' => 'Zamítnuto',
            'pending' => 'Zpracovává se',
        ],
        'edit' => [
            'title' => 'Upravit aktivitu',
            'distance' => 'Vzdálenost (km):',
            'elevation' => 'Převýšení (m):',
            'activity' => 'Aktivita:',
            'submit' => 'Uložit',
        ],
        'preview' => [
            'button_label' => 'Náhled',
            'title' => 'Náhled aktivity',
        ],
        'comment' => 'Komentář',
        'distance' => 'Vzdálenost',
        'elevation' => 'Převýšení',
        'activity' => 'Aktivita',
    ],
    'validation' => [
        'email' => [
            'invalid' => 'Zadaný e-mail je neplatný',
            'not_blank' => 'E-mail nesmí být prázdný',
        ],
    ],
    'image_upload' => [
        'dropzone_text' => 'Přetáhněte soubor sem nebo klikněte pro výběr',
        'choose_file' => 'Vybrat soubor',
    ],
];
