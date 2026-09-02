<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthController extends Controller
{
    /**
     * Diesen Monat: open tasks due after this week, through the end of this calendar month.
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');
        [$endOfWeek, $monthCutoff] = $this->weekAndMonthCutoffs();

        $tasks = $this->currentUser()->tasks()
            ->where('status', 'open')
            ->whereDate('due_at', '>', $endOfWeek)
            ->whereDate('due_at', '<=', $monthCutoff)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('month', ['tasks' => $tasks, 'area' => $area]);
    }
}
