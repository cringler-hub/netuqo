<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DoneController extends Controller
{
    public function index(Request $request): View
    {
        $area = $request->query('area');

        $base = $this->currentUser()->tasks()->where('status', 'done');

        $tasks = (clone $base)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderByDesc('completed_at')
            ->get();

        return view('done', ['tasks' => $tasks, 'area' => $area, 'counts' => $this->areaCounts($base)]);
    }
}
