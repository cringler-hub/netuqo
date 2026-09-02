<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TodayController extends Controller
{
    /**
     * Heute: open tasks due today or overdue — what needs attention now.
     * Open tasks due later (or with no due date) live on the "Später" screen instead.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');

        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<=', now())
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('today', ['tasks' => $tasks, 'area' => $area]);
    }
}
