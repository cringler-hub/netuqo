<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LaterController extends Controller
{
    /**
     * Später: open tasks due after this month, or with no due date at all.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');
        [, $monthCutoff] = $this->weekAndMonthCutoffs();

        $base = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->where(function ($query) use ($monthCutoff) {
                $query->whereNull('due_at')->orWhereDate('due_at', '>', $monthCutoff);
            });

        $tasks = (clone $base)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderByRaw('due_at is null')
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('later', ['tasks' => $tasks, 'area' => $area, 'counts' => $this->areaCounts($base)]);
    }
}
