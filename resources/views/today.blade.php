<x-layouts.app :title="'netuqo · Heute'" active="today">
    <section class="mb-10">
        <h1 class="text-3xl font-semibold tracking-tight text-text">Heute.</h1>
        <p class="mt-1 text-text-muted">Was ist wichtig.</p>
    </section>

    <section class="mb-10">
        <form method="POST" action="{{ route('tasks.store') }}" x-data="{ area: '' }">
            @csrf
            <div class="rounded-[var(--radius-task)] border border-border bg-surface p-4 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                <input
                    type="text"
                    name="title"
                    required
                    autocomplete="off"
                    placeholder="Was möchtest du festhalten?"
                    class="w-full border-none bg-transparent p-0 text-base text-text placeholder:text-text-muted focus:outline-none focus:ring-0"
                >
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide">
                    <input
                        type="date"
                        name="due_at"
                        x-ref="dueAt"
                        class="rounded-full border border-border bg-transparent px-3 py-1 text-text-muted focus:border-primary focus:text-text focus:outline-none"
                    >
                    <button
                        type="button"
                        @click="$refs.dueAt.value = '{{ now()->format('Y-m-d') }}'"
                        class="rounded-full border border-border px-3 py-1 text-text-muted transition-colors hover:text-text"
                    >Heute</button>
                    <input type="hidden" name="area" :value="area">
                    <button
                        type="button"
                        @click="area = area === 'business' ? '' : 'business'"
                        :class="area === 'business' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted'"
                        class="rounded-full border px-3 py-1 transition-colors"
                    >Business</button>
                    <button
                        type="button"
                        @click="area = area === 'private' ? '' : 'private'"
                        :class="area === 'private' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted'"
                        class="rounded-full border px-3 py-1 transition-colors"
                    >Privat</button>
                    <button
                        type="submit"
                        class="btn-gradient ml-auto rounded px-4 py-1.5 text-sm normal-case tracking-normal text-white"
                    >Hinzufügen</button>
                </div>
            </div>
        </form>
        @error('title')
            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror
    </section>

    <x-area-filter route="today" :area="$area" />

    <section class="flex flex-col">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Noch nichts erfasst. Trag oben deine erste Aufgabe ein.</p>
        @endforelse
    </section>
</x-layouts.app>
