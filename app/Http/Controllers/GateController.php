<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Temporary site-wide password gate — see DECISIONS.md. Deliberately not real
 * per-user authentication (no username, no per-user session identity); replaced
 * once real login (username/password + Google/Microsoft/Apple) ships, per ROADMAP.md.
 */
class GateController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('gate_unlocked')) {
            return redirect()->route('today');
        }

        return view('gate');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        $configured = (string) config('gate.password');

        if ($configured === '' || ! hash_equals($configured, (string) $request->string('password'))) {
            return back()->withErrors(['password' => 'Falsches Passwort.']);
        }

        $request->session()->put('gate_unlocked', true);
        $request->session()->regenerate();

        return redirect()->intended(route('today'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('gate_unlocked');
        $request->session()->regenerate();

        return redirect()->route('gate.show');
    }
}
