<?php

namespace App\CustomLogic;

interface ExtraPoints
{
    public static function acceptWeeks();
    public static function reward(): int;
    public static function getWinners();
}
