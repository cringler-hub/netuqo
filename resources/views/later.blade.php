<x-layouts.app :title="'netuqo · Später'" active="later">
    <section class="mb-10">
        <h1 class="text-3xl font-semibold tracking-tight text-text">Später.</h1>
        <p class="mt-1 text-text-muted">Alles, was noch nicht heute dran ist.</p>
    </section>

    <x-area-filter route="later" :area="$area" />

    @if ($thisWeek->isEmpty() && $thisMonth->isEmpty() && $later->isEmpty())
        <p class="text-text-muted">Nichts für später vorgemerkt.</p>
    @else
        @if ($thisWeek->isNotEmpty())
            <section class="mb-8">
                <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">Diese Woche</h2>
                <div class="flex flex-col">
                    @foreach ($thisWeek as $task)
                        <x-task-row :task="$task" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($thisMonth->isNotEmpty())
            <section class="mb-8">
                <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">Diesen Monat</h2>
                <div class="flex flex-col">
                    @foreach ($thisMonth as $task)
                        <x-task-row :task="$task" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($later->isNotEmpty())
            <section>
                <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-text-muted">Später</h2>
                <div class="flex flex-col">
                    @foreach ($later as $task)
                        <x-task-row :task="$task" />
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</x-layouts.app>
