<?php

declare(strict_types=1);

namespace App\Notifications\EmailTemplate;

use App\Entity\Season;
use App\Notifications\AbstractEmailTemplate;

final class SeasonStartTemplate extends AbstractEmailTemplate
{
    public function __construct(Season $season)
    {
        $this->setContext('season', $season);
    }

    #[\Override]
    public function getSubject(): string
    {
        return 'Měsíční Vytrvalec - Zahájení nové sezóny';
    }

    #[\Override]
    public function getTemplate(): string
    {
        return 'emails/season_start.twig';
    }
}
