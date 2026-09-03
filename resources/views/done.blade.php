<x-layouts.app :title="'netuqo · Erledigt'" active="done">
    <section class="mb-10">
        <h1 class="font-headline text-3xl font-medium tracking-tight text-text">Erledigt.</h1>
        <p class="font-claim mt-1 text-text-muted">Was geschafft ist, bleibt.</p>
    </section>

    <x-area-filter route="done" :area="$area" />

    <section class="flex flex-col gap-3">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Noch nichts erledigt.</p>
        @endforelse
    </section>
</x-layouts.app>
