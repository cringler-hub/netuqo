<x-layouts.app :title="'netuqo · Heute'">
    <header class="border-b border-border">
        <div class="mx-auto flex max-w-2xl items-center justify-between px-6 py-4">
            <span class="text-lg font-semibold tracking-tight">netuqo</span>
            <nav class="flex items-center gap-6 text-sm text-text-muted">
                <a href="#" class="font-medium text-primary">Heute</a>
                <a href="#" class="hover:text-text">Erledigt</a>
                <a href="#" class="hover:text-text">Einstellungen</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-2xl px-6 py-16">
        <section class="mb-10">
            <h1 class="text-3xl font-light tracking-tight">Guten Morgen.</h1>
            <p class="mt-1 text-text-muted">Hier ist, was heute wichtig ist.</p>
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
                        class="w-full border-none bg-transparent p-0 text-base placeholder:text-text-muted focus:outline-none focus:ring-0"
                    >
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                        <input
                            type="date"
                            name="due_at"
                            class="rounded-full border border-border bg-transparent px-3 py-1 text-text-muted focus:border-primary focus:text-text focus:outline-none"
                        >
                        <input type="hidden" name="area" :value="area">
                        <button
                            type="button"
                            @click="area = area === 'business' ? '' : 'business'"
                            :class="area === 'business' ? 'border-primary text-primary' : 'border-border text-text-muted'"
                            class="rounded-full border px-3 py-1 transition-colors"
                        >Business</button>
                        <button
                            type="button"
                            @click="area = area === 'private' ? '' : 'private'"
                            :class="area === 'private' ? 'border-primary text-primary' : 'border-border text-text-muted'"
                            class="rounded-full border px-3 py-1 transition-colors"
                        >Privat</button>
                        <button
                            type="submit"
                            class="ml-auto rounded-full bg-primary px-4 py-1.5 text-white transition-colors hover:bg-primary-hover"
                        >Hinzufügen</button>
                    </div>
                </div>
            </form>
            @error('title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </section>

        <section class="flex flex-col">
            @forelse ($tasks as $task)
                <div class="flex items-center gap-4 rounded-[var(--radius-task)] px-2 py-3 -mx-2">
                    <span class="h-6 w-6 shrink-0 rounded-full border border-border"></span>
                    <span class="flex-grow text-base text-text">{{ $task->title }}</span>
                    <div class="flex shrink-0 items-center gap-2 text-sm text-text-muted">
                        @if ($task->area)
                            <span class="rounded-full bg-background px-2 py-0.5 text-xs">{{ $task->area === 'business' ? 'Business' : 'Privat' }}</span>
                        @endif
                        @if ($task->due_at)
                            <span>{{ $task->due_at->format('d.m.') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-text-muted">Noch nichts erfasst. Trag oben deine erste Aufgabe ein.</p>
            @endforelse
        </section>
    </main>
</x-layouts.app>
