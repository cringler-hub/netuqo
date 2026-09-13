@props(['route', 'area', 'counts' => null])

<div class="mb-6 flex items-center gap-2 text-xs uppercase tracking-wide">
    <a
        href="{{ route($route) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ ! $area ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Alle{{ $counts ? ' ('.$counts['all'].')' : '' }}</a>
    <a
        href="{{ route($route, ['area' => 'business']) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $area === 'business' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Business{{ $counts ? ' ('.$counts['business'].')' : '' }}</a>
    <a
        href="{{ route($route, ['area' => 'private']) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $area === 'private' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Privat{{ $counts ? ' ('.$counts['private'].')' : '' }}</a>
</div>
