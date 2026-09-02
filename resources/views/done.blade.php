<x-layouts.app :title="'netuqo · Erledigt'" active="done">
    <section class="mb-10">
        <h1 class="font-heading text-3xl font-semibold tracking-tight text-text">Erledigt.</h1>
        <p class="mt-1 text-text-muted">Was du schon geschafft hast.</p>
    </section>

    <x-area-filter route="done" :area="$area" />

    <section class="flex flex-col">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Noch nichts erledigt.</p>
        @endforelse
    </section>
</x-layouts.app>
