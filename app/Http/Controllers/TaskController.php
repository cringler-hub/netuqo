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

        $task = $this->currentUser()->tasks()->create($validated);

        $task->activities()->create([
            'user_id' => $task->user_id,
            'action' => 'created',
            'context' => [
                'title' => $task->title,
                'area' => $task->area,
                'due_at' => $task->due_at?->format('Y-m-d'),
            ],
        ]);

        return redirect()->route('today');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'due_at' => ['nullable', 'date'],
            'area' => ['nullable', 'in:business,private'],
        ]);

        $before = [
            'title' => $task->title,
            'due_at' => $task->due_at?->format('Y-m-d'),
            'area' => $task->area,
        ];

        $task->update($validated);

        $after = [
            'title' => $task->title,
            'due_at' => $task->due_at?->format('Y-m-d'),
            'area' => $task->area,
        ];

        foreach (['title', 'due_at', 'area'] as $field) {
            if (array_key_exists($field, $validated) && $before[$field] !== $after[$field]) {
                $task->activities()->create([
                    'user_id' => $task->user_id,
                    'action' => "{$field}_changed",
                    'old_value' => $before[$field],
                    'new_value' => $after[$field],
                ]);
            }
        }

        return redirect()->back();
    }

    public function complete(Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $wasOverdue = $task->due_at !== null && $task->due_at->isBefore(now()->startOfDay());

        $task->complete();
        $task->activities()->create([
            'user_id' => $task->user_id,
            'action' => 'completed',
            'old_value' => 'open',
            'new_value' => 'done',
            'context' => [
                'area' => $task->area,
                'was_overdue' => $wasOverdue,
            ],
        ]);

        return redirect()->back();
    }

    public function reopen(Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $task->reopen();
        $task->activities()->create([
            'user_id' => $task->user_id,
            'action' => 'reopened',
            'old_value' => 'done',
            'new_value' => 'open',
            'context' => [
                'area' => $task->area,
            ],
        ]);

        return redirect()->back();
    }

    public function destroy(Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $this->currentUser()->id, 403);

        $task->activities()->create([
            'user_id' => $task->user_id,
            'action' => 'deleted',
            'old_value' => $task->title,
            'context' => [
                'area' => $task->area,
                'due_at' => $task->due_at?->format('Y-m-d'),
                'status' => $task->status,
            ],
        ]);

        $task->delete();

        return redirect()->back();
    }
}
