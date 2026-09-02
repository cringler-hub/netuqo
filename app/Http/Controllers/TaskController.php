<?php

namespace App\Http\Controllers;

use App\Models\Task;
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

    public function complete(Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $task->complete();
        $task->activities()->create(['user_id' => $task->user_id, 'action' => 'completed', 'old_value' => 'open', 'new_value' => 'done']);

        return redirect()->back();
    }

    public function reopen(Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $task->reopen();
        $task->activities()->create(['user_id' => $task->user_id, 'action' => 'reopened', 'old_value' => 'done', 'new_value' => 'open']);

        return redirect()->back();
    }
}
