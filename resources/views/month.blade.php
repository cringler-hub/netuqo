<x-layouts.app :title="'netuqo · Diesen Monat'" active="month">
    <section class="mb-10">
        <h1 class="font-headline text-3xl font-medium tracking-tight text-text">Diesen Monat.</h1>
        <p class="mt-1 text-text-muted">Was diesen Monat noch dran ist.</p>
    </section>

    <x-area-filter route="month" :area="$area" />

    <section class="flex flex-col gap-3">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Nichts für diesen Monat vorgemerkt.</p>
        @endforelse
    </section>
</x-layouts.app>
