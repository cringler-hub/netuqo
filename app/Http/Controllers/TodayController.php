<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TodayController extends Controller
{
    /**
     * Heute: open tasks due today or overdue — what needs attention now. Split into two
     * groups so overdue tasks (a cleanup list) don't crowd out what's actually due today
     * (the main event) — see DECISIONS.md. Open tasks due later (or with no due date)
     * live on the "Später" screen instead.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');

        $base = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<=', now());

        $tasks = (clone $base)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        [$overdueTasks, $todayTasks] = $tasks->partition(
            fn ($task) => $task->due_at->isBefore(now()->startOfDay())
        );

        return view('today', [
            'todayTasks' => $todayTasks,
            'overdueTasks' => $overdueTasks,
            'area' => $area,
            'counts' => $this->areaCounts($base),
        ]);
    }
}
