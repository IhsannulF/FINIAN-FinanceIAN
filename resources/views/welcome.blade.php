<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FINIAN — Kelola uang, akhirnya masuk akal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logofinian.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'IBM Plex Sans', Helvetica, Arial, sans-serif; }
        .font-display { font-family: 'IBM Plex Sans', Helvetica, Arial, sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body class="bg-white text-[#101114] antialiased">

    <!-- Header dengan Alpine.js untuk Mobile Menu -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-[#dedee5]" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4 flex items-center justify-between">
            
            <div class="flex items-center gap-2">
                <!-- Tombol Hamburger (Mobile) -->
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-[#101114] p-1 rounded-md hover:bg-gray-100 transition">
                    <svg x-show="!mobileMenuOpen" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                
                <img src="{{ asset('images/logofinian.png') }}" alt="FINIAN" class="h-7 md:h-8 w-auto">
                <span class="font-display font-bold text-lg md:text-xl">FINIAN</span>
            </div>
            
            <!-- Menu Desktop -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#686b82]">
                <a href="#fitur" class="hover:text-[#101114] transition">Fitur</a>
                <a href="#insight" class="hover:text-[#101114] transition">AI Insight</a>
                <a href="#cara-kerja" class="hover:text-[#101114] transition">Cara kerja</a>
            </nav>

            <!-- Aksi Auth Desktop -->
            <div class="flex items-center gap-3">
                @auth
                    @php
                        $userName = Auth::user()->name ?? 'User';
                        $nameParts = explode(' ', $userName);
                        $initials = count($nameParts) > 1
                            ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                            : strtoupper(substr($userName, 0, 2));
                    @endphp
                    
                    <!-- Logo Inisial (Sekarang tampil di semua layar) -->
                    <div class="w-8 h-8 rounded-full bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center font-bold text-sm shrink-0">
                        {{ $initials }}
                    </div>
                    
                    <!-- Nama (Sembunyi di HP, Tampil di Laptop) -->
                    <span class="hidden md:block text-sm font-medium text-[#101114] max-w-[150px] lg:max-w-[250px] truncate">
                        {{ $userName }}
                    </span>
                    
                    <!-- Tombol (Sembunyi di HP, Tampil di Laptop) -->
                    <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white hover:bg-[#5741d8] transition">
                        Ke Dashboard
                    </a>
                @else
                    <!-- Jika belum login, sembunyikan tombol di HP karena sudah ada di menu Hamburger -->
                    <div class="hidden md:flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-[#101114] hover:text-[#7132f5] transition">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white hover:bg-[#5741d8] transition">
                            Daftar gratis
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Menu Mobile Dropdown -->
        <div x-show="mobileMenuOpen" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-[#dedee5] bg-white px-4 py-4 shadow-whisper">
            <nav class="flex flex-col gap-4 text-sm font-medium text-[#686b82]">
                <a href="#fitur" @click="mobileMenuOpen = false" class="hover:text-[#101114]">Fitur</a>
                <a href="#insight" @click="mobileMenuOpen = false" class="hover:text-[#101114]">AI Insight</a>
                <a href="#cara-kerja" @click="mobileMenuOpen = false" class="hover:text-[#101114]">Cara kerja</a>
                <div class="h-px w-full bg-[#dedee5] my-1"></div>
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white hover:bg-[#5741d8] transition">
                        Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-[#101114] hover:text-[#7132f5]">Masuk</a>
                    <a href="{{ route('register') }}" class="w-full text-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white hover:bg-[#5741d8] transition">
                        Daftar gratis
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:pt-16 md:pb-20 grid md:grid-cols-2 gap-10 md:gap-12 items-center">
        <div>
            <span class="inline-block rounded-[6px] bg-[rgba(20,158,97,0.16)] px-3 py-1 text-[11px] md:text-xs font-semibold text-[#026b3f] mb-5 md:mb-6">
                Dibangun untuk mahasiswa & pekerja muda
            </span>
            <!-- Ukuran font responsif (text-4xl di HP, 48px di Laptop) -->
            <h1 class="font-display font-bold text-4xl md:text-[48px] leading-tight md:leading-[1.17] tracking-[-1px] mb-4 md:mb-6">
                Uang kamu,<br>akhirnya masuk akal.
            </h1>
            <p class="text-sm md:text-base leading-relaxed md:leading-[1.38] text-[#686b82] mb-6 md:mb-8 max-w-md">
                Catat transaksi, atur budget bulanan, dan biarkan AI menjelaskan ke mana uangmu pergi — tanpa perlu buka spreadsheet.
            </p>
            <div class="flex flex-wrap items-center gap-3 md:gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-3 md:px-4 md:py-[13px] text-sm md:text-base font-semibold text-white hover:bg-[#5741d8] transition w-full sm:w-auto justify-center">
                        Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-3 md:px-4 md:py-[13px] text-sm md:text-base font-semibold text-white hover:bg-[#5741d8] transition w-full sm:w-auto justify-center">
                        Mulai gratis
                    </a>
                @endauth
                <a href="#fitur" class="inline-flex items-center rounded-[12px] border border-[#5741d8] px-4 py-3 md:px-4 md:py-[13px] text-sm md:text-base font-medium text-[#5741d8] hover:bg-[rgba(133,91,251,0.08)] transition w-full sm:w-auto justify-center">
                    Lihat fitur
                </a>
            </div>
        </div>

        <!-- Hero Card Mockup -->
        <div class="relative mt-4 md:mt-0">
            <div class="rounded-[16px] bg-white shadow-[0_4px_24px_rgba(0,0,0,0.03)] border border-[#dedee5] p-5 md:p-6">
                <p class="text-[11px] md:text-xs font-medium text-[#9497a9] mb-1">Saldo bulan ini</p>
                <p class="font-display font-bold text-2xl md:text-[28px] tracking-tight md:tracking-[-0.5px] mb-4 break-words">Rp1.980.000</p>
                <div class="grid grid-cols-2 gap-2 md:gap-3 mb-4 md:mb-5">
                    <div class="rounded-[10px] bg-[rgba(148,151,169,0.08)] p-3">
                        <p class="text-[10px] md:text-xs text-[#9497a9] mb-1">Pemasukan</p>
                        <p class="text-xs md:text-sm font-semibold break-words">Rp2.500.000</p>
                    </div>
                    <div class="rounded-[10px] bg-[rgba(148,151,169,0.08)] p-3">
                        <p class="text-[10px] md:text-xs text-[#9497a9] mb-1">Pengeluaran</p>
                        <p class="text-xs md:text-sm font-semibold break-words">Rp520.000</p>
                    </div>
                </div>
                <div class="rounded-[12px] bg-[rgba(133,91,251,0.16)] p-3 md:p-4">
                    <p class="text-[11px] md:text-xs font-semibold text-[#7132f5] mb-1.5">FINIAN AI Insight</p>
                    <p class="text-xs md:text-sm text-[#101114] leading-relaxed md:leading-[1.38]">
                        &ldquo;Pengeluaran Makanan kamu 40% dari total budget bulan ini — sedikit lebih tinggi dari biasanya. Coba kurangi jajan luar 2x minggu ini.&rdquo;
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pain Points Section -->
    <section class="border-y border-[#dedee5] bg-[rgba(148,151,169,0.05)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8 text-sm text-[#686b82]">
            <p><span class="font-display font-bold text-[#101114] block mb-1">Uang habis sebelum akhir bulan</span>Tanpa catatan, sulit tahu ke mana perginya.</p>
            <p><span class="font-display font-bold text-[#101114] block mb-1">Susah nabung konsisten</span>Niat nabung sering kalah sama godaan jajan.</p>
            <p><span class="font-display font-bold text-[#101114] block mb-1">Gak ngerti pola belanja sendiri</span>Tahu boros, tapi gak tahu kenapa.</p>
        </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
        <h2 class="font-display font-bold text-3xl md:text-[36px] leading-tight md:leading-[1.22] tracking-[-0.5px] mb-3">Semua yang kamu butuh, satu tempat</h2>
        <p class="text-sm md:text-base text-[#686b82] mb-10 md:mb-12 max-w-xl">Gak perlu spreadsheet ribet. FINIAN nyatetin, ngitung, dan ngejelasin — kamu tinggal lihat.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
            <div class="rounded-[16px] border border-[#dedee5] p-5 md:p-6 bg-white">
                <div class="w-10 h-10 rounded-[10px] bg-[rgba(133,91,251,0.16)] flex items-center justify-center mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                </div>
                <h3 class="font-semibold text-base md:text-[18px] mb-2">Catat transaksi</h3>
                <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">Tambah pemasukan dan pengeluaran dalam beberapa detik, lengkap dengan kategori.</p>
            </div>

            <div class="rounded-[16px] border border-[#dedee5] p-5 md:p-6 bg-white">
                <div class="w-10 h-10 rounded-[10px] bg-[rgba(133,91,251,0.16)] flex items-center justify-center mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18"/></svg>
                </div>
                <h3 class="font-semibold text-base md:text-[18px] mb-2">Atur budget bulanan</h3>
                <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">Set satu angka budget, FINIAN yang ngitung sisa dan progresnya buat kamu.</p>
            </div>

            <div class="rounded-[16px] border border-[#dedee5] p-5 md:p-6 bg-white">
                <div class="w-10 h-10 rounded-[10px] bg-[rgba(133,91,251,0.16)] flex items-center justify-center mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/></svg>
                </div>
                <h3 class="font-semibold text-base md:text-[18px] mb-2">Pantau per kategori</h3>
                <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">Lihat kategori mana yang paling banyak makan budget kamu bulan ini.</p>
            </div>
        </div>
    </section>

    <!-- AI Insight Section -->
    <section id="insight" class="bg-[#101114] text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 grid md:grid-cols-2 gap-10 md:gap-12 items-center">
            <div class="order-2 md:order-1">
                <div class="w-10 h-10 rounded-[10px] bg-[rgba(133,91,251,0.24)] flex items-center justify-center mb-5">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#AFA9EC" stroke-width="2"><path d="M12 2l2.4 7.2H22l-6 4.4 2.3 7.2L12 16.4 5.7 20.8 8 13.6 2 9.2h7.6z"/></svg>
                </div>
                <h2 class="font-display font-bold text-3xl md:text-[36px] leading-tight md:leading-[1.22] tracking-[-0.5px] mb-4">
                    AI yang benar-benar ngerti kebiasaan belanjamu
                </h2>
                <p class="text-sm md:text-base text-[#9497a9] leading-relaxed md:leading-[1.38] mb-6 max-w-md">
                    Setiap bulan, FINIAN membaca pola transaksimu — kategori mana yang paling boros, apakah kamu on-track sama budget, dan kasih satu tips singkat yang gampang dipahami. Bukan grafik ribet, cukup dua-tiga kalimat.
                </p>
                <ul class="space-y-3 text-[13px] md:text-sm text-[#9497a9]">
                    <li class="flex items-start gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2" class="mt-0.5 shrink-0"><path d="M20 6 9 17l-5-5"/></svg>
                        Highlight kategori pengeluaran terbesar bulan ini
                    </li>
                    <li class="flex items-start gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2" class="mt-0.5 shrink-0"><path d="M20 6 9 17l-5-5"/></svg>
                        Kasih tahu apakah kamu masih aman dari budget
                    </li>
                    <li class="flex items-start gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7132f5" stroke-width="2" class="mt-0.5 shrink-0"><path d="M20 6 9 17l-5-5"/></svg>
                        Satu tips actionable yang bisa langsung dipraktekkan
                    </li>
                </ul>
            </div>

            <div class="order-1 md:order-2 rounded-[16px] bg-[#1a1b20] border border-[rgba(255,255,255,0.08)] p-5 md:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-[8px] bg-[#7132f5] flex items-center justify-center">
                        <svg width="14" height="14" class="md:w-4 md:h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2l2.4 7.2H22l-6 4.4 2.3 7.2L12 16.4 5.7 20.8 8 13.6 2 9.2h7.6z"/></svg>
                    </div>
                    <p class="text-sm font-semibold">FINIAN AI Insight</p>
                </div>
                <p class="text-sm md:text-base leading-relaxed md:leading-[1.38] text-white">
                    &ldquo;Pengeluaran Makanan kamu 40% dari total budget bulan ini — sedikit lebih tinggi dari biasanya. Coba kurangi jajan luar 2x minggu ini.&rdquo;
                </p>
                <div class="mt-5 pt-5 border-t border-[rgba(255,255,255,0.08)] flex items-center justify-between text-[11px] md:text-xs text-[#9497a9]">
                    <span>Diperbarui otomatis tiap bulan</span>
                    <span class="text-[#AFA9EC] font-medium">Live dari transaksimu</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara Kerja Section -->
    <section id="cara-kerja" class="bg-[rgba(148,151,169,0.05)] border-y border-[#dedee5]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            <h2 class="font-display font-bold text-3xl md:text-[36px] leading-tight md:leading-[1.22] tracking-[-0.5px] mb-8 md:mb-12">Cara kerjanya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 md:gap-10">
                <div>
                    <p class="text-[11px] md:text-xs font-semibold text-[#7132f5] mb-2">01</p>
                    <h3 class="font-semibold text-base md:text-[18px] mb-2">Catat</h3>
                    <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">Input pemasukan dan pengeluaran harian, pilih kategorinya.</p>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs font-semibold text-[#7132f5] mb-2">02</p>
                    <h3 class="font-semibold text-base md:text-[18px] mb-2">Pantau</h3>
                    <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">Dashboard nunjukin saldo, sisa budget, dan sebaran pengeluaran secara real-time.</p>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs font-semibold text-[#7132f5] mb-2">03</p>
                    <h3 class="font-semibold text-base md:text-[18px] mb-2">Dapat insight</h3>
                    <p class="text-sm text-[#686b82] leading-relaxed md:leading-[1.38]">AI merangkum kebiasaan belanjamu bulan ini dalam satu-dua kalimat yang jelas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 text-center">
        <h2 class="font-display font-bold text-3xl md:text-[36px] leading-tight md:leading-[1.22] tracking-[-0.5px] mb-3 md:mb-4">Mulai kelola uangmu hari ini</h2>
        <p class="text-sm md:text-base text-[#686b82] mb-6 md:mb-8 max-w-md mx-auto">Gratis, gak perlu kartu kredit. Daftar dan catat transaksi pertamamu dalam satu menit.</p>
        @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-3 md:px-4 md:py-[13px] text-sm md:text-base font-semibold text-white hover:bg-[#5741d8] transition w-full sm:w-auto justify-center">
                Ke Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center rounded-[12px] bg-[#7132f5] px-4 py-3 md:px-4 md:py-[13px] text-sm md:text-base font-semibold text-white hover:bg-[#5741d8] transition w-full sm:w-auto justify-center">
                Daftar gratis
            </a>
        @endauth
    </section>

    <!-- Footer -->
    <footer class="border-t border-[#dedee5]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logofinian.png') }}" alt="FINIAN" class="h-5 md:h-6 w-auto">
                <span class="font-display font-bold text-sm">FINIAN</span>
            </div>
            <p class="text-[11px] md:text-xs text-[#9497a9]">10th IndonesiaNEXT</p>
        </div>
    </footer>

</body>
</html>