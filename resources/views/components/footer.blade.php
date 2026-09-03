<footer class="border-t border-border">
    <div class="mx-auto flex max-w-2xl flex-wrap items-center justify-center gap-x-3 gap-y-2 px-6 py-8 text-sm text-text-muted">
        <a href="{{ route('today') }}" class="flex items-center transition-opacity hover:opacity-80">
            <img src="{{ asset('images/logo-claim.svg') }}" alt="netuqo – Simply know what's next." class="h-10 w-auto dark:hidden">
            <img src="{{ asset('images/logo-claim-dark.svg') }}" alt="netuqo – Simply know what's next." class="hidden h-10 w-auto dark:block">
        </a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('impressum') }}" class="hover:text-text">Impressum</a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('datenschutz') }}" class="hover:text-text">Datenschutz</a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('agb') }}" class="hover:text-text">AGB</a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('kontakt') }}" class="hover:text-text">Kontakt</a>
    </div>
</footer>
