<header class="border-b border-neutral-border">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logofinian.png') }}" alt="FINIAN" class="h-8 w-auto">
                <span class="font-display font-bold text-lg">FINIAN</span>
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-neutral-gray">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Dashboard</a>
            <a href="{{ route('transactions') }}" class="{{ request()->routeIs('transactions') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Transaksi</a>
            <a href="{{ route('budget') }}" class="{{ request()->routeIs('budget') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Budget</a>
        </nav>
        <div class="flex items-center gap-3 relative" x-data="{ userMenuOpen: false }">
            @php
                $userName = Auth::user()->name ?? 'User';
                $nameParts = explode(' ', $userName);
                $initials = count($nameParts) > 1
                    ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                    : strtoupper(substr($userName, 0, 2));
            @endphp

            <button type="button" @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false"
                    class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-subtle text-brand flex items-center justify-center font-bold text-sm">
                    {{ $initials }}
                </div>
                <span class="text-sm font-medium">{{ $userName }}</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     class="text-neutral-gray transition" :class="userMenuOpen ? 'rotate-180' : ''">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <div x-show="userMenuOpen" x-cloak
                 class="absolute right-0 top-full mt-2 w-48 rounded-[12px] border border-neutral-border bg-white shadow-whisper overflow-hidden z-50">
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-2.5 text-sm text-neutral-black hover:bg-neutral-50 transition">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-semantic-red hover:bg-neutral-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>