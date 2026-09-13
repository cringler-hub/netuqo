<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CurrentUser;
use App\Support\TaskWindow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

abstract class Controller
{
    /**
     * netuqo is single-user for now — login is a deliberately later increment (see
     * ROADMAP.md). Every row is still scoped to a real user_id from day one; this just
     * resolves which one until real authentication exists.
     */
    protected function currentUser(): User
    {
        return CurrentUser::resolve();
    }

    /**
     * @return array{0: Carbon, 1: Carbon} [endOfWeek, monthCutoff]
     */
    protected function weekAndMonthCutoffs(): array
    {
        return TaskWindow::cutoffs();
    }

    /**
     * Counts for the area-filter chips (Alle/Business/Privat), for the given screen's
     * own bucket of tasks — independent of whichever area filter is currently applied,
     * so a chip always shows what you'd see if you switched to it.
     *
     * @return array{all: int, business: int, private: int}
     */
    protected function areaCounts(Builder|Relation $query): array
    {
        return [
            'all' => (clone $query)->count(),
            'business' => (clone $query)->where('area', 'business')->count(),
            'private' => (clone $query)->where('area', 'private')->count(),
        ];
    }
}
