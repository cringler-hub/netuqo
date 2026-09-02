<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class WeekController extends Controller
{
    /**
     * Diese Woche: open tasks due after today, through the end of this calendar week.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');
        [$endOfWeek] = $this->weekAndMonthCutoffs();

        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->whereDate('due_at', '>', now())
            ->whereDate('due_at', '<=', $endOfWeek)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('week', ['tasks' => $tasks, 'area' => $area]);
    }
}
