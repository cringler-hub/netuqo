<x-layouts.app :title="'netuqo · Später'" active="later">
    <section class="mb-10">
        <h1 class="font-headline text-3xl font-medium tracking-tight text-text">Später.</h1>
        <p class="font-claim mt-1 text-text-muted">Was noch Zeit hat.</p>
    </section>

    <x-area-filter route="later" :area="$area" />

    <section class="flex flex-col gap-3">
        @forelse ($tasks as $task)
            <x-task-row :task="$task" />
        @empty
            <p class="text-text-muted">Nichts für später vorgemerkt.</p>
        @endforelse
    </section>
</x-layouts.app>
