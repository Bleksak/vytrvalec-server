<?php

namespace App\CustomLogic;

use App\Entity\Season;

interface ExtraPoints
{
    public static function getUniqueName(): string;
    public static function reward(): int;

    /**
     * @return array<int, mixed>
     */
    public function calculate(Season $season): array;
}
