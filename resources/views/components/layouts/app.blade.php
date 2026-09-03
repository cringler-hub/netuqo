<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name') }}</title>

        <x-theme-script />

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-text antialiased">
        <header class="sticky top-0 z-10 border-b border-border bg-surface/85 backdrop-blur-xl">
            <div class="relative mx-auto max-w-2xl px-6 py-4">
                <div class="flex flex-wrap items-center gap-y-3 pr-10">
                    <a href="{{ route('today') }}" class="mr-6 flex items-center sm:mr-8">
                        <img src="{{ asset('images/logo-claim.svg') }}" alt="netuqo – Simply know what's next." class="h-10 w-auto dark:hidden">
                        <img src="{{ asset('images/logo-claim-dark.svg') }}" alt="netuqo – Simply know what's next." class="hidden h-10 w-auto dark:block">
                    </a>
                    <nav class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm sm:gap-6">
                        <a href="{{ route('today') }}" class="{{ ($active ?? '') === 'today' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Heute</a>
                        <a href="{{ route('week') }}" class="{{ ($active ?? '') === 'week' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Diese Woche</a>
                        <a href="{{ route('month') }}" class="{{ ($active ?? '') === 'month' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Dieser Monat</a>
                        <a href="{{ route('later') }}" class="{{ ($active ?? '') === 'later' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Später</a>
                        <a href="{{ route('done') }}" class="{{ ($active ?? '') === 'done' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Erledigt</a>
                    </nav>
                </div>
                <div class="absolute right-6 top-4" x-data="{ menuOpen: false }">
                    <button
                        type="button"
                        @click="menuOpen = ! menuOpen"
                        aria-label="Menü"
                        class="rounded-full p-1.5 text-text-muted transition-colors hover:text-text"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <circle cx="10" cy="4" r="1.5" />
                            <circle cx="10" cy="10" r="1.5" />
                            <circle cx="10" cy="16" r="1.5" />
                        </svg>
                    </button>
                    <div
                        x-show="menuOpen"
                        x-cloak
                        @click.outside="menuOpen = false"
                        class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-border bg-surface py-1 shadow-[0_4px_24px_rgba(11,16,32,0.08)]"
                    >
                        <button
                            type="button"
                            @click="
                                document.documentElement.classList.toggle('dark');
                                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                                menuOpen = false;
                            "
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-text-muted hover:bg-text/5 hover:text-text"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="hidden h-4 w-4 dark:block">
                                <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 1 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 dark:hidden">
                                <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd" />
                            </svg>
                            <span class="dark:hidden">Nachtmodus</span>
                            <span class="hidden dark:inline">Tagmodus</span>
                        </button>
                        <form method="POST" action="{{ route('gate.destroy') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-left text-sm text-text-muted hover:bg-text/5 hover:text-text">
                                Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-16">
            {{ $slot }}
        </main>

        <x-footer />
    </body>
</html>
