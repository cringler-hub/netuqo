<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LaterController extends Controller
{
    /**
     * Später: open tasks not due today or overdue — no due date, or due in the future.
     * Grouped into Diese Woche / Diesen Monat / Später so the list stays scannable
     * without adding separate nav pages (MANIFESTO.md caps main nav at 3–4 entries).
     */
    public function index(Request $request): View
    {
        $area = $request->query('area');
        $range = in_array($request->query('range'), ['week', 'month', 'later'], true) ? $request->query('range') : null;

        $endOfWeek = now()->endOfWeek();
        $endOfMonth = now()->endOfMonth();
        $monthCutoff = $endOfMonth->greaterThan($endOfWeek) ? $endOfMonth : $endOfWeek;

        $baseQuery = fn () => $this->currentUser()->tasks()
            ->where('status', 'open')
            ->when($area, fn ($query) => $query->where('area', $area));

        $thisWeek = $baseQuery()
            ->whereDate('due_at', '>', now())
            ->whereDate('due_at', '<=', $endOfWeek)
            ->orderBy('due_at')->orderBy('created_at')->get();

        $thisMonth = $baseQuery()
            ->whereDate('due_at', '>', $endOfWeek)
            ->whereDate('due_at', '<=', $monthCutoff)
            ->orderBy('due_at')->orderBy('created_at')->get();

        $later = $baseQuery()
            ->where(function ($query) use ($monthCutoff) {
                $query->whereNull('due_at')->orWhereDate('due_at', '>', $monthCutoff);
            })
            ->orderByRaw('due_at is null')
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->get();

        return view('later', [
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'later' => $later,
            'area' => $area,
            'range' => $range,
        ]);
    }
}
