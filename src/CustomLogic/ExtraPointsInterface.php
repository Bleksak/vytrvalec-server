<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\ExtraPointsResultDto;
use App\Entity\Season;

interface ExtraPointsInterface
{
    public static function getUniqueName(): string;

    public static function reward(): int;

    public static function getWeek(): int;

    /**
     * @return list<ExtraPointsResultDto>
     */
    public function calculate(Season $season): array;
}
