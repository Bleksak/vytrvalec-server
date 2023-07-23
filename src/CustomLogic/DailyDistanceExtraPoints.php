<?php

namespace App\CustomLogic;

class DailyDistanceExtraPoints implements ExtraPoints
{

    public static function acceptWeeks(): array
    {
        return [2];
    }

    public static function reward(): int
    {
        return 1;
    }

    public static function getWinners(): array
    {
        $winners = [];



        return $winners;
    }
}