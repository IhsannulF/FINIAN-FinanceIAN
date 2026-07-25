<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - FINIAN</title>

    <!-- Favicon FINIAN -->
    <link rel="icon" type="image/png" href="{{ asset('images/logofinian.png') }}">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="antialiased bg-white text-neutral-black">

    @include('partials.navbar')
    
    <!-- Main Content -->
    <!-- py-6 untuk HP agar jarak tidak terlalu jauh, md:py-10 untuk laptop -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">

        <!-- Tempat Menampilkan Pesan Error / Sukses -->
        @if(session('error'))
            <div class="mb-5 md:mb-6 p-3 md:p-4 rounded-[12px] bg-semantic-red/10 border border-semantic-red text-semantic-red text-sm md:text-base font-medium flex items-center gap-2">
                <i class="ph ph-warning-circle text-lg md:text-xl shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 md:mb-6 p-3 md:p-4 rounded-[12px] bg-semantic-greentext/10 border border-semantic-greentext text-semantic-greentext text-sm md:text-base font-medium flex items-center gap-2">
                <i class="ph ph-check-circle text-lg md:text-xl shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        <!-- Berakhir Tempat Pesan -->
        
        <h1 class="font-display font-bold text-3xl md:text-[36px] tracking-[-0.5px] mb-6 md:mb-8">Dashboard</h1>

        <!-- Top Section: Balance & Quick Actions -->
        <div class="rounded-[16px] bg-white border border-neutral-border p-5 md:p-6 shadow-whisper mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 md:gap-6">
                <div>
                    <p class="text-xs md:text-sm font-medium text-neutral-silver mb-1">Total Saldo Aktif</p>
                    <!-- Menggunakan text-4xl di HP dan break-words agar teks panjang turun ke bawah -->
                    <h2 class="font-display font-bold text-4xl md:text-[48px] tracking-[-1px] leading-tight md:leading-[1.17] break-words">
                        Rp{{ number_format($balance, 0, ',', '.') }}
                    </h2>
                </div>
                <!-- Tombol full-width di HP (w-full), flex-row mulai ukuran sm (tablet) -->
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <a href="/transactions?type=income" class="w-full sm:w-auto bg-white border border-brand-dark text-brand-dark rounded-[12px] px-4 py-3 md:px-[16px] md:py-[13px] font-semibold text-sm md:text-base hover:bg-neutral-50 transition shadow-micro text-center">
                        + Pemasukan
                    </a>
                    <a href="/transactions?type=expense" class="w-full sm:w-auto bg-brand text-white rounded-[12px] px-4 py-3 md:px-[16px] md:py-[13px] font-semibold text-sm md:text-base hover:bg-brand-dark transition shadow-micro text-center">
                        - Pengeluaran
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-5 mb-6 md:mb-8">
            <div class="rounded-[16px] border border-neutral-border p-4 md:p-5 shadow-whisper bg-white">
                <p class="text-[11px] md:text-xs font-medium text-neutral-gray mb-1.5 md:mb-2">Pemasukan Bulan Ini</p>
                <p class="font-display font-bold text-xl md:text-[22px] break-words">Rp{{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-[16px] border border-neutral-border p-4 md:p-5 shadow-whisper bg-white">
                <p class="text-[11px] md:text-xs font-medium text-neutral-gray mb-1.5 md:mb-2">Pengeluaran Bulan Ini</p>
                <p class="font-display font-bold text-xl md:text-[22px] break-words">Rp{{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <!-- Menggunakan sm:col-span-2 md:col-span-1 agar di tablet mengisi kolom kosong dengan rapi -->
            <div class="rounded-[16px] border border-neutral-border p-4 md:p-5 shadow-whisper bg-white sm:col-span-2 md:col-span-1">
                <p class="text-[11px] md:text-xs font-medium text-neutral-gray mb-1.5 md:mb-2">Sisa Budget (Rp{{ number_format($totalBudget, 0, ',', '.') }})</p>
                <p class="font-display font-bold text-xl md:text-[22px] break-words {{ $budgetRemaining < 0 ? 'text-semantic-red' : 'text-semantic-greentext' }}">
                    Rp{{ number_format($budgetRemaining, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Bottom Section: Expense Monitoring & AI Insight -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Expense Monitoring -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-[16px] border border-neutral-border p-5 md:p-6 shadow-whisper bg-white">
                    <h3 class="font-display font-semibold text-base md:text-[18px] mb-4 md:mb-5">Penggunaan Budget</h3>
                    
                    <!-- Progress Bar -->
                    <div class="mb-2 flex flex-col sm:flex-row sm:items-center justify-between text-xs md:text-sm gap-1">
                        <span class="font-medium text-neutral-black">Terpakai: Rp{{ number_format($expense, 0, ',', '.') }}</span>
                        <span class="text-neutral-silver">{{ $budgetPercentage }}% dari Rp{{ number_format($totalBudget, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="w-full bg-neutral-border rounded-[9999px] h-2.5 md:h-3 mb-6 md:mb-8 overflow-hidden">
                        <div class="bg-brand h-full rounded-[9999px]" @style(["width: {$budgetPercentage}%"])></div>
                    </div>

                    <h3 class="font-display font-semibold text-base md:text-[18px] mb-4">Pengeluaran per Kategori</h3>
                    <div class="space-y-4">
                        
                        @forelse ($expensesByCategory as $categoryExpense)
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="shrink-0 w-10 h-10 rounded-[10px] bg-neutral-50 border border-neutral-border flex items-center justify-center text-[20px] md:text-[22px]" @style(["color: " . ($categoryExpense->category->color ?? '#7132f5')])>
                                    <i class="{{ $categoryExpense->category->icon ?? 'ph ph-sparkle' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-sm md:text-base text-neutral-black leading-[1.38] truncate">{{ $categoryExpense->category->name }}</p>
                                    <p class="text-[11px] md:text-xs text-neutral-gray mt-0.5">{{ $categoryExpense->total_trx }} Transaksi</p>
                                </div>
                            </div>
                            <p class="font-semibold text-sm md:text-base text-neutral-black whitespace-nowrap shrink-0">Rp{{ number_format($categoryExpense->total_amount, 0, ',', '.') }}</p>
                        </div>
                        @empty
                        <p class="text-xs md:text-sm text-neutral-gray text-center py-4">Belum ada pengeluaran bulan ini.</p>
                        @endforelse

                    </div>
                </div>
            </div>

            <!-- Right Column: AI Insight -->
            <div class="lg:col-span-1">
                <div class="rounded-[16px] bg-brand-subtle border border-[#d6c7ff] p-5 md:p-6 shadow-whisper relative">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="shrink-0 w-7 h-7 md:w-8 md:h-8 rounded-[8px] bg-brand flex items-center justify-center shadow-micro">
                            <svg width="14" height="14" class="md:w-4 md:h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2l2.4 7.2H22l-6 4.4 2.3 7.2L12 16.4 5.7 20.8 8 13.6 2 9.2h7.6z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-base md:text-[18px] text-brand-dark">FINIAN AI Insight</h3>
                    </div>
                    
                    <p class="text-sm md:text-base text-brand-dark leading-[1.4] md:leading-[1.38] font-medium">
                        @if(isset($aiInsight) && $aiInsight)
                            "{{ $aiInsight->content }}"
                        @else
                            "Belum ada analisa keuangan untuk bulan ini. Klik tombol di bawah untuk meminta AI merangkum kebiasaan belanjamu."
                        @endif
                    </p>

                    <form action="{{ route('dashboard.insight') }}" method="POST" class="mt-5 md:mt-6" 
                        onsubmit="let btn = document.getElementById('ai-submit-btn'); btn.disabled = true; btn.innerHTML = `<i class='ph ph-spinner animate-spin text-lg mr-2'></i> Menganalisa...`;">
                        @csrf
                        
                        <button id="ai-submit-btn" type="submit" 
                            class="w-full flex items-center justify-center bg-white text-brand rounded-[10px] py-2 md:py-[10px] text-sm font-semibold shadow-micro hover:bg-neutral-50 transition border border-[#d6c7ff] disabled:opacity-50">
                            {{ (isset($aiInsight) && $aiInsight) ? 'Perbarui Analisa' : 'Buat Analisa AI' }}
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

</body>
</html>