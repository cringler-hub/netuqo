<x-layouts.guest :title="'netuqo · Login'">
    <div class="flex min-h-[70vh] flex-col items-center justify-center px-6 py-16">
        <a href="{{ route('gate.show') }}" class="mb-10 flex items-center">
            <img src="{{ asset('images/logo-claim.svg') }}" alt="netuqo – Simply know what's next." class="h-12 w-auto dark:hidden">
            <img src="{{ asset('images/logo-claim-dark.svg') }}" alt="netuqo – Simply know what's next." class="hidden h-12 w-auto dark:block">
        </a>

        <form method="POST" action="{{ route('gate.authenticate') }}" class="w-full max-w-sm">
            @csrf
            <div class="rounded-[var(--radius-task)] border border-border bg-surface p-6 shadow-[0_4px_24px_rgba(11,16,32,0.03)]">
                <label for="password" class="font-claim block text-sm text-text-muted">Passwort</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autofocus
                    autocomplete="current-password"
                    class="mt-2 w-full rounded-full border border-border bg-transparent px-4 py-2 text-base text-text placeholder:text-text-muted focus:border-primary focus:outline-none focus:ring-[1.5px] focus:ring-primary"
                >
                @error('password')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn-primary mt-4 w-full rounded-full px-4 py-2 text-sm">Anmelden</button>
            </div>
        </form>
    </div>
</x-layouts.guest>
