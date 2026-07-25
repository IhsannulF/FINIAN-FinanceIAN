<header class="border-b border-neutral-border">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logofinian.png') }}" alt="FINIAN" class="h-8 w-auto">
                <span class="font-display font-bold text-lg">FINIAN</span>
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-neutral-gray">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Dashboard</a>
            <a href="{{ route('transactions') }}" class="{{ request()->routeIs('transactions') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Transaksi</a>
            <a href="{{ route('budget') }}" class="{{ request()->routeIs('budget') ? 'text-neutral-black' : 'hover:text-neutral-black' }}">Budget</a>
        </nav>
        <div class="flex items-center gap-3">
            @php
                $userName = Auth::user()->name ?? 'User';
                $nameParts = explode(' ', $userName);
                $initials = count($nameParts) > 1
                    ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                    : strtoupper(substr($userName, 0, 2));
            @endphp
            <div class="w-8 h-8 rounded-full bg-brand-subtle text-brand flex items-center justify-center font-bold text-sm">
                {{ $initials }}
            </div>
            <span class="text-sm font-medium">{{ $userName }}</span>
        </div>
    </div>
</header>