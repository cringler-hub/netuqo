<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TodayController extends Controller
{
    public function index(): View
    {
        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->orderByRaw('due_at is null')
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('today', ['tasks' => $tasks]);
    }
}
