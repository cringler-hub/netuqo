<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_at' => ['nullable', 'date'],
            'area' => ['nullable', 'in:business,private'],
        ]);

        $this->currentUser()->tasks()->create($validated);

        return redirect()->route('today');
    }
}
