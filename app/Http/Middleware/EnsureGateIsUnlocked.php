<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Temporary site-wide password gate (see DECISIONS.md) — not real per-user
 * authentication. Sends anyone without an unlocked session to the gate screen.
 */
class EnsureGateIsUnlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('gate_unlocked')) {
            return redirect()->guest(route('gate.show'));
        }

        return $next($request);
    }
}
