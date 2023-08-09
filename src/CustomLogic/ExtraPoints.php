<?php

namespace App\CustomLogic;

use App\Entity\Submission;

interface ExtraPoints
{
    public static function getUniqueName(): string;
    public static function acceptsWeek(int $week): bool;
    public static function reward(): int;
    public function requiresActivity(): bool;
    public function accumulate(Submission $submission): void;
    public function finalize(): void;
    public function getWinners(): array;
}
