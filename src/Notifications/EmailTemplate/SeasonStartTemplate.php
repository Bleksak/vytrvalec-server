<?php

namespace App\Notifications\EmailTemplate;

use App\Entity\Season;
use App\Notifications\EmailTemplate;

final class SeasonStartTemplate extends EmailTemplate
{
    public function __construct(Season $season)
    {
        $this->context = [
            'season' => $season,
        ];
    }

    public function getSubject(): string
    {
        return 'Měsíční Vytrvalec - Nová sezóna';
    }

    public function getTemplate(): string
    {
        return 'emails/season_start.twig';
    }
}
