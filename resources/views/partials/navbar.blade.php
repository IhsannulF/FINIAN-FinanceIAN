<header class="border-b border-neutral-border bg-white" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4 flex items-center justify-between">
        
        <!-- Bagian Kiri: Tombol Menu HP & Logo -->
        <div class="flex items-center gap-3 md:gap-2">
            <!-- Tombol Hamburger (Hanya muncul di HP) -->
            <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-neutral-black p-1 hover:bg-neutral-50 rounded-md transition">
                <!-- Ikon Menu (Garis Tiga) -->
                <svg x-show="!mobileMenuOpen" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <!-- Ikon Tutup (X) -->
                <svg x-show="mobileMenuOpen" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>

            <!-- Logo FINIAN -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logofinian.png') }}" alt="FINIAN" class="h-7 md:h-8 w-auto">
                <span class="font-display font-bold text-lg md:text-xl">FINIAN</span>
            </a>
        </div>

        <!-- Bagian Tengah: Menu Desktop (Hilang di HP) -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-neutral-gray">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-neutral-black' : 'hover:text-neutral-black transition' }}">Dashboard</a>
            <a href="{{ route('transactions') }}" class="{{ request()->routeIs('transactions') ? 'text-neutral-black' : 'hover:text-neutral-black transition' }}">Transaksi</a>
            <a href="{{ route('budget') }}" class="{{ request()->routeIs('budget') ? 'text-neutral-black' : 'hover:text-neutral-black transition' }}">Budget</a>
        </nav>

        <!-- Bagian Kanan: Menu User -->
        <div class="flex items-center gap-3 relative">
            @php
                $userName = Auth::user()->name ?? 'User';
                $nameParts = explode(' ', $userName);
                $initials = count($nameParts) > 1
                    ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                    : strtoupper(substr($userName, 0, 2));
            @endphp

            <button type="button" @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false"
                    class="flex items-center gap-2 md:gap-3 rounded-[12px] p-1 pr-2 hover:bg-neutral-50 transition">
                <div class="w-8 h-8 rounded-full bg-brand-subtle text-brand flex items-center justify-center font-bold text-sm shrink-0">
                    {{ $initials }}
                </div>
                <!-- Nama disembunyikan di HP layar kecil (sm), agar tidak menabrak logo -->
                <span class="hidden sm:block text-sm font-medium truncate max-w-[120px] lg:max-w-[200px] text-left">{{ $userName }}</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     class="text-neutral-gray transition duration-200" :class="userMenuOpen ? 'rotate-180' : ''">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <!-- Dropdown User Desktop & Mobile -->
            <div x-show="userMenuOpen" x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 top-full mt-2 w-48 rounded-[12px] border border-neutral-border bg-white shadow-whisper overflow-hidden z-50">
                
                <!-- Info nama untuk HP (karena disembunyikan di luar) -->
                <div class="block sm:hidden px-4 py-3 border-b border-neutral-border bg-neutral-50">
                    <p class="text-sm font-semibold text-neutral-black truncate">{{ $userName }}</p>
                </div>

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

    <!-- Bagian Bawah: Menu Navigasi Mobile (Merespons Tombol Hamburger) -->
    <div x-show="mobileMenuOpen" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-neutral-border bg-neutral-50">
        <nav class="flex flex-col px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-[8px] text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-white text-brand shadow-sm border border-neutral-border' : 'text-neutral-gray hover:bg-white hover:text-neutral-black' }}">Dashboard</a>
            <a href="{{ route('transactions') }}" class="block px-3 py-2 rounded-[8px] text-sm font-medium transition {{ request()->routeIs('transactions') ? 'bg-white text-brand shadow-sm border border-neutral-border' : 'text-neutral-gray hover:bg-white hover:text-neutral-black' }}">Transaksi</a>
            <a href="{{ route('budget') }}" class="block px-3 py-2 rounded-[8px] text-sm font-medium transition {{ request()->routeIs('budget') ? 'bg-white text-brand shadow-sm border border-neutral-border' : 'text-neutral-gray hover:bg-white hover:text-neutral-black' }}">Budget</a>
        </nav>
    </div>
</header>