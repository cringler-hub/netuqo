@props(['task'])

@php
    $isOverdue = $task->status === 'open' && $task->due_at && $task->due_at->isBefore(now()->startOfDay());
@endphp

<div class="flex items-center gap-4 rounded-[var(--radius-task)] px-2 py-3 -mx-2 transition-colors hover:bg-surface/50">
    <form method="POST" action="{{ $task->status === 'open' ? route('tasks.complete', $task) : route('tasks.reopen', $task) }}">
        @csrf
        <button
            type="submit"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border transition-colors {{ $task->status === 'done' ? 'border-success bg-success/20 text-success' : 'border-border text-transparent hover:border-primary' }}"
        >
            <span class="text-xs">✓</span>
        </button>
    </form>
    <div class="min-w-0 flex-grow" x-data="{ editingTitle: false }">
        <button
            type="button"
            x-show="!editingTitle"
            @click="editingTitle = true; $nextTick(() => $refs.titleEdit.focus())"
            class="block w-full truncate text-left text-base {{ $task->status === 'done' ? 'text-text-muted line-through' : 'text-text hover:text-primary' }}"
        >{{ $task->title }}</button>
        <form
            x-show="editingTitle"
            x-cloak
            method="POST"
            action="{{ route('tasks.update', $task) }}"
        >
            @csrf
            @method('PATCH')
            <input
                type="text"
                name="title"
                x-ref="titleEdit"
                value="{{ $task->title }}"
                required
                maxlength="255"
                @keydown.enter.prevent="$refs.titleEdit.form.requestSubmit()"
                @blur="$refs.titleEdit.form.requestSubmit()"
                class="w-full rounded border border-border bg-transparent px-1 py-0.5 text-base text-text focus:border-primary focus:outline-none"
            >
        </form>
    </div>
    <div class="flex shrink-0 items-center gap-2 text-xs text-text-muted">
        <div x-data="{ editingArea: false }">
            <button
                type="button"
                x-show="!editingArea"
                @click="editingArea = true"
                class="rounded-full bg-white/5 px-2 py-0.5 uppercase tracking-wide hover:text-text"
            >{{ $task->area ? ($task->area === 'business' ? 'Business' : 'Privat') : '+ Kategorie' }}</button>
            <form
                x-show="editingArea"
                x-cloak
                @click.outside="editingArea = false"
                method="POST"
                action="{{ route('tasks.update', $task) }}"
                x-ref="areaForm"
                class="flex items-center gap-1"
            >
                @csrf
                @method('PATCH')
                <input type="hidden" name="area" x-ref="areaInput" value="{{ $task->area }}">
                <button
                    type="button"
                    @click="$refs.areaInput.value = 'business'; $refs.areaForm.requestSubmit()"
                    class="rounded-full border px-2 py-0.5 uppercase tracking-wide {{ $task->area === 'business' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
                >Business</button>
                <button
                    type="button"
                    @click="$refs.areaInput.value = 'private'; $refs.areaForm.requestSubmit()"
                    class="rounded-full border px-2 py-0.5 uppercase tracking-wide {{ $task->area === 'private' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
                >Privat</button>
                @if ($task->area)
                    <button
                        type="button"
                        @click="$refs.areaInput.value = ''; $refs.areaForm.requestSubmit()"
                        aria-label="Kategorie entfernen"
                        class="text-text-muted hover:text-danger"
                    >&times;</button>
                @endif
            </form>
        </div>
        <div x-data="{ editingDate: false }">
            <button
                type="button"
                x-show="!editingDate"
                @click="editingDate = true"
                class="uppercase tracking-wide {{ $isOverdue ? 'font-semibold text-danger' : 'hover:text-text' }}"
            >
                {{ $isOverdue ? 'Überfällig · '.$task->due_at->format('d.m.') : ($task->due_at ? $task->due_at->format('d.m.') : '+ Datum') }}
            </button>
            <form
                x-show="editingDate"
                x-cloak
                @click.outside="editingDate = false"
                method="POST"
                action="{{ route('tasks.update', $task) }}"
                class="flex items-center gap-1"
            >
                @csrf
                @method('PATCH')
                <input
                    type="date"
                    name="due_at"
                    x-ref="dueAtEdit"
                    value="{{ $task->due_at?->format('Y-m-d') }}"
                    onchange="this.form.submit()"
                    class="rounded border border-border bg-transparent px-2 py-0.5 text-xs text-text focus:border-primary focus:outline-none"
                >
                <button
                    type="button"
                    @click="$refs.dueAtEdit.value = '{{ now()->format('Y-m-d') }}'; $refs.dueAtEdit.form.submit()"
                    class="rounded-full border border-border px-2 py-0.5 uppercase tracking-wide transition-colors hover:text-text"
                >Heute</button>
            </form>
        </div>
    </div>
    <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Aufgabe wirklich löschen?')">
        @csrf
        @method('DELETE')
        <button
            type="submit"
            aria-label="Löschen"
            class="flex h-6 w-6 shrink-0 items-center justify-center text-text-muted transition-colors hover:text-danger"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path fill-rule="evenodd" d="M8.75 1a.75.75 0 0 0-.75.75V3H4a.75.75 0 0 0 0 1.5h.322l.66 11.213A2.75 2.75 0 0 0 7.727 18h4.546a2.75 2.75 0 0 0 2.745-2.287L15.678 4.5H16a.75.75 0 0 0 0-1.5h-4v-1.25a.75.75 0 0 0-.75-.75h-2.5ZM10 6a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5A.75.75 0 0 1 10 6ZM7.25 6.75a.75.75 0 0 0-1.5.058l.35 7.5a.75.75 0 1 0 1.5-.07l-.35-7.488Zm6.5.058a.75.75 0 1 0-1.5-.058l-.35 7.5a.75.75 0 1 0 1.5.07l.35-7.512Z" clip-rule="evenodd" />
            </svg>
        </button>
    </form>
</div>
