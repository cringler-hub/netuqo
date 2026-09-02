@props(['route', 'area'])

<div class="mb-6 flex items-center gap-2 text-sm">
    <a
        href="{{ route($route) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ ! $area ? 'border-primary text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Alle</a>
    <a
        href="{{ route($route, ['area' => 'business']) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $area === 'business' ? 'border-primary text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Business</a>
    <a
        href="{{ route($route, ['area' => 'private']) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $area === 'private' ? 'border-primary text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Privat</a>
</div>
