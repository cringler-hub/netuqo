<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class Controller
{
    /**
     * netuqo is single-user for now — login is a deliberately later increment (see
     * ROADMAP.md). Every row is still scoped to a real user_id from day one; this just
     * resolves which one until real authentication exists.
     */
    protected function currentUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'owner@netuqo.com'],
            ['name' => 'Owner', 'password' => Hash::make(Str::random(40))],
        );
    }

    /**
     * Shared boundaries for the Diese Woche / Diesen Monat / Später split, so the three
     * pages can't drift out of sync on where one bucket ends and the next begins.
     *
     * @return array{0: Carbon, 1: Carbon} [endOfWeek, monthCutoff]
     */
    protected function weekAndMonthCutoffs(): array
    {
        $endOfWeek = now()->endOfWeek();
        $endOfMonth = now()->endOfMonth();
        $monthCutoff = $endOfMonth->greaterThan($endOfWeek) ? $endOfMonth : $endOfWeek;

        return [$endOfWeek, $monthCutoff];
    }
}
