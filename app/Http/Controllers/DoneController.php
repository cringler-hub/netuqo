<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DoneController extends Controller
{
    public function index(): View
    {
        $tasks = $this->currentUser()->tasks()
            ->where('status', 'done')
            ->orderByDesc('completed_at')
            ->get();

        return view('done', ['tasks' => $tasks]);
    }
}
