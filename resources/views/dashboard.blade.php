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
    <main class="max-w-6xl mx-auto px-6 py-10">

        <!-- Tempat Menampilkan Pesan Error / Sukses -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-[12px] bg-semantic-red/10 border border-semantic-red text-semantic-red font-medium flex items-center gap-2">
                    <i class="ph ph-warning-circle text-xl"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-[12px] bg-semantic-greentext/10 border border-semantic-greentext text-semantic-greentext font-medium flex items-center gap-2">
                    <i class="ph ph-check-circle text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif
        <!-- Berakhir Tempat Pesan -->
        
        <h1 class="font-display font-bold text-[36px] tracking-[-0.5px] mb-8">Dashboard</h1>

        <!-- Top Section: Balance & Quick Actions -->
        <div class="rounded-[16px] bg-white border border-neutral-border p-6 shadow-whisper mb-6">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <p class="text-sm font-medium text-neutral-silver mb-1">Total Saldo Aktif</p>
                    <h2 class="font-display font-bold text-[48px] tracking-[-1px] leading-[1.17]">
                        Rp{{ number_format($balance, 0, ',', '.') }}
                    </h2>
                </div>
                <div class="flex gap-3">
                    <a href="/transactions?type=income" class="bg-white border border-brand-dark text-brand-dark rounded-[12px] px-[16px] py-[13px] font-semibold text-base hover:bg-neutral-50 transition shadow-micro text-center">
                        + Pemasukan
                    </a>
                    <a href="/transactions?type=expense" class="bg-brand text-white rounded-[12px] px-[16px] py-[13px] font-semibold text-base hover:bg-brand-dark transition shadow-micro text-center">
                        - Pengeluaran
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="rounded-[16px] border border-neutral-border p-5 shadow-whisper bg-white">
                <p class="text-xs font-medium text-neutral-gray mb-2">Pemasukan Bulan Ini</p>
                <p class="font-display font-bold text-[22px]">Rp{{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-[16px] border border-neutral-border p-5 shadow-whisper bg-white">
                <p class="text-xs font-medium text-neutral-gray mb-2">Pengeluaran Bulan Ini</p>
                <p class="font-display font-bold text-[22px]">Rp{{ number_format($expense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-[16px] border border-neutral-border p-5 shadow-whisper bg-white">
                <p class="text-xs font-medium text-neutral-gray mb-2">Sisa Budget (Rp{{ number_format($totalBudget, 0, ',', '.') }})</p>
                <p class="font-display font-bold text-[22px] {{ $budgetRemaining < 0 ? 'text-semantic-red' : 'text-semantic-greentext' }}">
                    Rp{{ number_format($budgetRemaining, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Bottom Section: Expense Monitoring & AI Insight -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Expense Monitoring -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-[16px] border border-neutral-border p-6 shadow-whisper bg-white">
                    <h3 class="font-display font-semibold text-[18px] mb-5">Penggunaan Budget</h3>
                    
                    <!-- Progress Bar -->
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="font-medium text-neutral-black">Terpakai: Rp{{ number_format($expense, 0, ',', '.') }}</span>
                        <span class="text-neutral-silver">{{ $budgetPercentage }}% dari Rp{{ number_format($totalBudget, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="w-full bg-neutral-border rounded-[9999px] h-3 mb-8 overflow-hidden">
                        <!-- Menggunakan @style directive -->
                        <div class="bg-brand h-3 rounded-[9999px]" @style(["width: {$budgetPercentage}%"])></div>
                    </div>

                    <h3 class="font-display font-semibold text-[18px] mb-4">Pengeluaran per Kategori</h3>
                    <div class="space-y-4">
                        
                        @forelse ($expensesByCategory as $categoryExpense)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                
                                <!-- Menggunakan @style directive untuk warna dinamis -->
                                <div class="w-10 h-10 rounded-[10px] bg-neutral-50 border border-neutral-border flex items-center justify-center text-[22px]" @style(["color: " . ($categoryExpense->category->color ?? '#7132f5')])>
                                    <i class="{{ $categoryExpense->category->icon ?? 'ph ph-sparkle' }}"></i>
                                </div>
                                
                                <div>
                                    <p class="font-medium text-base text-neutral-black leading-[1.38]">{{ $categoryExpense->category->name }}</p>
                                    <p class="text-xs text-neutral-gray mt-0.5">{{ $categoryExpense->total_trx }} Transaksi</p>
                                </div>
                            </div>
                            <!-- TYPO SUDAH DIPERBAIKI DI SINI MENGGUNAKAN TAG <p> -->
                            <p class="font-semibold text-base text-neutral-black">Rp{{ number_format($categoryExpense->total_amount, 0, ',', '.') }}</p>
                        </div>
                        @empty
                        <p class="text-sm text-neutral-gray text-center py-4">Belum ada pengeluaran bulan ini.</p>
                        @endforelse

                    </div>
                </div>
            </div>

            <!-- Right Column: AI Insight -->
            <div class="lg:col-span-1">
                <div class="rounded-[16px] bg-brand-subtle border border-[#d6c7ff] p-6 shadow-whisper relative">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-[8px] bg-brand flex items-center justify-center shadow-micro">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2l2.4 7.2H22l-6 4.4 2.3 7.2L12 16.4 5.7 20.8 8 13.6 2 9.2h7.6z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[18px] text-brand-dark">FINIAN AI Insight</h3>
                    </div>
                    
                    <p class="text-base text-brand-dark leading-[1.38] font-medium">
                        @if(isset($aiInsight) && $aiInsight)
                            "{{ $aiInsight->content }}"
                        @else
                            "Belum ada analisa keuangan untuk bulan ini. Klik tombol di bawah untuk meminta AI merangkum kebiasaan belanjamu."
                        @endif
                    </p>

                    <!-- Form ini terhubung dengan route POST untuk trigger LLM API -->
                    <form action="{{ route('dashboard.insight') }}" method="POST" class="mt-6" 
                        onsubmit="let btn = document.getElementById('ai-submit-btn'); btn.disabled = true; btn.innerHTML = `<i class='ph ph-spinner animate-spin text-lg mr-2'></i> Menganalisa...`;">
                        @csrf
                        
                        <button id="ai-submit-btn" type="submit" 
                            class="w-full flex items-center justify-center bg-white text-brand rounded-[10px] py-[10px] text-sm font-semibold shadow-micro hover:bg-neutral-50 transition border border-[#d6c7ff] disabled:opacity-50">
                            {{ (isset($aiInsight) && $aiInsight) ? 'Perbarui Analisa' : 'Buat Analisa AI' }}
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

</body>
</html>