<?php

declare(strict_types=1);

namespace App\Notifications\EmailTemplate;

use App\Entity\Season;
use App\Notifications\EmailTemplate;

final class SeasonStartTemplate extends EmailTemplate
{
    public function __construct(Season $season)
    {
        $this->setContext('season', $season);
    }

    public function getSubject(): string
    {
        return 'Měsíční Vytrvalec - Zahájení nové sezóny';
    }

    public function getTemplate(): string
    {
        return 'emails/season_start.twig';
    }
}
