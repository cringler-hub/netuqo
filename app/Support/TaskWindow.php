<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class TaskWindow
{
    /**
     * Shared boundaries for the Diese Woche / Dieser Monat / Später split, so every
     * screen (and the nav counts) agree on where one bucket ends and the next begins.
     *
     * @return array{0: Carbon, 1: Carbon} [endOfWeek, monthCutoff]
     */
    public static function cutoffs(): array
    {
        $endOfWeek = now()->endOfWeek();
        $endOfMonth = now()->endOfMonth();
        $monthCutoff = $endOfMonth->greaterThan($endOfWeek) ? $endOfMonth : $endOfWeek;

        return [$endOfWeek, $monthCutoff];
    }
}
