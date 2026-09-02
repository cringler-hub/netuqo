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
            <input
                type="text"
                placeholder="Was möchtest du festhalten?"
                class="w-full rounded-[var(--radius-task)] border border-border bg-surface px-4 py-3 text-base placeholder:text-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
            >
        </section>

        <section x-data="{
            tasks: [
                { title: 'Angebot Müller freigeben', done: false },
                { title: 'Sarah wegen Budget anrufen', done: false },
                { title: 'Q3 Strategie-Draft prüfen', done: false },
                { title: 'Flug nach München buchen', done: false },
            ],
        }" class="flex flex-col">
            <template x-for="(task, index) in tasks" :key="index">
                <div class="group flex items-center gap-4 rounded-[var(--radius-task)] px-2 py-3 -mx-2 transition-colors hover:bg-surface">
                    <button
                        @click="task.done = !task.done"
                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border transition-colors"
                        :class="task.done ? 'border-success bg-success/20 text-success' : 'border-border text-transparent hover:border-primary'"
                    >
                        <span class="text-xs">✓</span>
                    </button>
                    <span class="text-base" :class="task.done ? 'text-text-muted line-through' : 'text-text'" x-text="task.title"></span>
                </div>
            </template>
        </section>
    </main>
</x-layouts.app>
