@props(['area' => null, 'range' => null])

<div class="mb-3 flex items-center gap-2 text-xs uppercase tracking-wide">
    <a
        href="{{ route('later', array_filter(['area' => $area])) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ ! $range ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Alle</a>
    <a
        href="{{ route('later', array_filter(['area' => $area, 'range' => 'week'])) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $range === 'week' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Diese Woche</a>
    <a
        href="{{ route('later', array_filter(['area' => $area, 'range' => 'month'])) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $range === 'month' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Diesen Monat</a>
    <a
        href="{{ route('later', array_filter(['area' => $area, 'range' => 'later'])) }}"
        class="rounded-full border px-3 py-1 transition-colors {{ $range === 'later' ? 'border-primary bg-primary/10 text-primary' : 'border-border text-text-muted hover:text-text' }}"
    >Später</a>
</div>
