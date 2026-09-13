<?php

namespace App\View\Composers;

use App\Support\CurrentUser;
use App\Support\TaskWindow;
use Illuminate\View\View;

/**
 * Nav-bar counts (Heute (n), Diese Woche (n), ...) — see DECISIONS.md. Only computed
 * once the gate is unlocked: the legal pages (Impressum etc.) share this same layout
 * but are deliberately reachable without logging in, and must never leak how many
 * tasks exist to an anonymous visitor.
 */
class NavCountsComposer
{
    public function compose(View $view): void
    {
        if (! session('gate_unlocked')) {
            $view->with('navCounts', null);

            return;
        }

        $tasks = CurrentUser::resolve()->tasks();
        [$endOfWeek, $monthCutoff] = TaskWindow::cutoffs();

        $view->with('navCounts', [
            'today' => (clone $tasks)->where('status', 'open')->whereNotNull('due_at')->whereDate('due_at', '<=', now())->count(),
            'week' => (clone $tasks)->where('status', 'open')->whereDate('due_at', '>', now())->whereDate('due_at', '<=', $endOfWeek)->count(),
            'month' => (clone $tasks)->where('status', 'open')->whereDate('due_at', '>', $endOfWeek)->whereDate('due_at', '<=', $monthCutoff)->count(),
            'later' => (clone $tasks)->where('status', 'open')->where(function ($query) use ($monthCutoff) {
                $query->whereNull('due_at')->orWhereDate('due_at', '>', $monthCutoff);
            })->count(),
            'done' => (clone $tasks)->where('status', 'done')->count(),
        ]);
    }
}
