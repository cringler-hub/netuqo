<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-text antialiased">
        <header class="sticky top-0 z-10 border-b border-border bg-surface/85 backdrop-blur-xl">
            <div class="mx-auto flex max-w-2xl flex-wrap items-center justify-between gap-y-3 px-6 py-4">
                <a href="{{ route('today') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="" class="h-6 w-auto">
                    <span class="font-headline text-lg font-medium tracking-tight text-text">netuqo</span>
                </a>
                <nav class="flex w-full flex-wrap items-center gap-x-4 gap-y-1 text-sm sm:w-auto sm:flex-nowrap sm:gap-6">
                    <a href="{{ route('today') }}" class="{{ ($active ?? '') === 'today' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Heute</a>
                    <a href="{{ route('week') }}" class="{{ ($active ?? '') === 'week' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Diese Woche</a>
                    <a href="{{ route('month') }}" class="{{ ($active ?? '') === 'month' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Diesen Monat</a>
                    <a href="{{ route('later') }}" class="{{ ($active ?? '') === 'later' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Später</a>
                    <a href="{{ route('done') }}" class="{{ ($active ?? '') === 'done' ? 'font-semibold text-text' : 'text-text-muted hover:text-text' }}">Erledigt</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-16">
            {{ $slot }}
        </main>
    </body>
</html>
