<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background text-text antialiased">
        <header class="border-b border-border">
            <div class="mx-auto flex max-w-2xl items-center justify-between px-6 py-4">
                <span class="text-lg font-semibold tracking-tight">netuqo</span>
                <nav class="flex items-center gap-6 text-sm text-text-muted">
                    <a href="{{ route('today') }}" class="{{ ($active ?? '') === 'today' ? 'font-medium text-primary' : 'hover:text-text' }}">Heute</a>
                    <a href="{{ route('later') }}" class="{{ ($active ?? '') === 'later' ? 'font-medium text-primary' : 'hover:text-text' }}">Später</a>
                    <a href="{{ route('done') }}" class="{{ ($active ?? '') === 'done' ? 'font-medium text-primary' : 'hover:text-text' }}">Erledigt</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-16">
            {{ $slot }}
        </main>
    </body>
</html>
