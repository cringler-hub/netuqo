@props(['task'])

@php
    $isOverdue = $task->status === 'open' && $task->due_at && $task->due_at->isBefore(now()->startOfDay());
@endphp

<div class="flex items-center gap-4 rounded-[var(--radius-task)] px-2 py-3 -mx-2">
    <form method="POST" action="{{ $task->status === 'open' ? route('tasks.complete', $task) : route('tasks.reopen', $task) }}">
        @csrf
        <button
            type="submit"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border transition-colors {{ $task->status === 'done' ? 'border-success bg-success/20 text-success' : 'border-border text-transparent hover:border-primary' }}"
        >
            <span class="text-xs">✓</span>
        </button>
    </form>
    <span class="flex-grow text-base {{ $task->status === 'done' ? 'text-text-muted line-through' : 'text-text' }}">{{ $task->title }}</span>
    <div class="flex shrink-0 items-center gap-2 text-sm text-text-muted" x-data="{ editingDate: false }">
        @if ($task->area)
            <span class="rounded-full bg-background px-2 py-0.5 text-xs">{{ $task->area === 'business' ? 'Business' : 'Privat' }}</span>
        @endif
        <button
            type="button"
            x-show="!editingDate"
            @click="editingDate = true"
            class="{{ $isOverdue ? 'font-medium text-red-600 hover:text-red-700' : 'hover:text-text' }}"
        >
            {{ $isOverdue ? 'Überfällig · '.$task->due_at->format('d.m.') : ($task->due_at ? $task->due_at->format('d.m.') : '+ Datum') }}
        </button>
        <form
            x-show="editingDate"
            x-cloak
            @click.outside="editingDate = false"
            method="POST"
            action="{{ route('tasks.update', $task) }}"
        >
            @csrf
            @method('PATCH')
            <input
                type="date"
                name="due_at"
                value="{{ $task->due_at?->format('Y-m-d') }}"
                onchange="this.form.submit()"
                class="rounded-full border border-border bg-transparent px-2 py-0.5 text-xs text-text focus:border-primary focus:outline-none"
            >
        </form>
    </div>
    <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Aufgabe wirklich löschen?')">
        @csrf
        @method('DELETE')
        <button
            type="submit"
            aria-label="Löschen"
            class="flex h-6 w-6 shrink-0 items-center justify-center text-text-muted transition-colors hover:text-red-600"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path fill-rule="evenodd" d="M8.75 1a.75.75 0 0 0-.75.75V3H4a.75.75 0 0 0 0 1.5h.322l.66 11.213A2.75 2.75 0 0 0 7.727 18h4.546a2.75 2.75 0 0 0 2.745-2.287L15.678 4.5H16a.75.75 0 0 0 0-1.5h-4v-1.25a.75.75 0 0 0-.75-.75h-2.5ZM10 6a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5A.75.75 0 0 1 10 6ZM7.25 6.75a.75.75 0 0 0-1.5.058l.35 7.5a.75.75 0 1 0 1.5-.07l-.35-7.488Zm6.5.058a.75.75 0 1 0-1.5-.058l-.35 7.5a.75.75 0 1 0 1.5.07l.35-7.512Z" clip-rule="evenodd" />
            </svg>
        </button>
    </form>
</div>
