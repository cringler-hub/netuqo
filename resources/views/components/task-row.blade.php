@props(['task'])

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
        <button type="button" x-show="!editingDate" @click="editingDate = true" class="hover:text-text">
            {{ $task->due_at ? $task->due_at->format('d.m.') : '+ Datum' }}
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
</div>
