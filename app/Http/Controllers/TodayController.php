<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TodayController extends Controller
{
    /**
     * Heute: open tasks due today or overdue — what needs attention now.
     * Open tasks due later (or with no due date) live on the "Später" screen instead.
     */
    public function index(): View
    {
        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<=', now())
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('today', ['tasks' => $tasks]);
    }
}
