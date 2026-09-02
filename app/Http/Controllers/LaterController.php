<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LaterController extends Controller
{
    /**
     * Später: open tasks not due today or overdue — no due date, or due in the future.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');

        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('due_at')->orWhereDate('due_at', '>', now());
            })
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderByRaw('due_at is null')
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('later', ['tasks' => $tasks, 'area' => $area]);
    }
}
