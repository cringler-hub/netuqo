<x-layouts.app :title="'netuqo · Diese Woche'" active="week">
    <section class="mb-10">
        <h1 class="font-headline text-3xl font-medium tracking-tight text-text">Diese Woche.</h1>
        <p class="font-claim mt-1 text-text-muted">Was als Nächstes zählt.</p>
    </section>

    <x-area-filter route="week" :area="$area" />

    <section class="flex flex-col gap-3">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Nichts für diese Woche vorgemerkt.</p>
        @endforelse
    </section>
</x-layouts.app>
