<?php

declare(strict_types=1);

namespace App\Utils;

final readonly class WeekCalculator
{
    public static function calculateWeekCount(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
    ): int {
        $diff = $end->diff($start);
        \assert(
            $diff->days !== false,
            'DateInterval created by diff cannot have days = false',
        );

        $weeks = \intdiv($diff->days + 1, 7);

        return $weeks === 0 ? 1 : $weeks;
    }

    public static function calculateWeekNumber(
        \DateTimeInterface $start,
        \DateTimeInterface $targetDate,
    ): int {
        $diff = $targetDate->diff($start);
        $days = $diff->days;

        if ($days === false) {
            $days = 0;
        }

        return $days > 0 ? \intdiv($days - 1, 7) : 0;
    }
}
