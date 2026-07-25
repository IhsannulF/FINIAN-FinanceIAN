<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budget Bulanan - FINIAN</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logofinian.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-neutral-black">

    @include('partials.navbar')

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-10">

        @if (session('success'))
            <div class="mb-6 rounded-[12px] bg-semantic-greenbg border border-[rgba(20,158,97,0.24)] text-semantic-greentext text-sm font-medium px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="font-display font-bold text-[36px] tracking-[-0.5px]">Budget Bulanan</h1>
            <p class="text-neutral-gray mt-2 text-base">Atur batas pengeluaranmu untuk bulan <span class="font-semibold text-neutral-black">{{ now()->translatedFormat('F Y') }}</span>.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Kolom Kiri: Form Input Budget -->
            <div>
                <div class="rounded-[16px] border border-neutral-border bg-white shadow-whisper p-6">
                    <h2 class="font-display font-semibold text-[22px] mb-6">Atur Total Budget</h2>

                    <form action="{{ route('budget.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="budget_amount" class="block text-sm font-medium text-neutral-black mb-2">Total Budget (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-neutral-gray font-medium">Rp</span>
                                </div>
                                <input type="number" name="budget_amount" id="budget_amount" value="{{ old('budget_amount', $totalBudget > 0 ? $totalBudget : '') }}" min="0" step="1"
                                    class="block w-full pl-12 pr-4 py-4 border border-neutral-border rounded-[12px] text-lg font-semibold text-neutral-black focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition"
                                    placeholder="0" required>
                            </div>
                            @error('budget_amount')
                                <p class="text-xs text-semantic-red mt-2">{{ $message }}</p>
                            @else
                                <p class="text-xs text-neutral-silver mt-2">Masukkan satu angka total batas pengeluaran bulan ini.</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-brand text-white rounded-[12px] px-[16px] py-[13px] font-semibold text-base hover:bg-brand-dark transition shadow-micro">
                            Simpan Budget
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Status & Progress -->
            <div>
                <div class="rounded-[16px] border border-neutral-border bg-neutral-50 shadow-whisper p-6 h-full flex flex-col justify-center">
                    <h2 class="font-display font-semibold text-[22px] mb-2">Status Pemakaian</h2>
                    <p class="text-sm text-neutral-gray mb-8">Berdasarkan total transaksi pengeluaran bulan ini.</p>

                    @if ($totalBudget > 0)
                        <!-- Angka Utama -->
                        <div class="mb-6">
                            <p class="text-sm font-medium text-neutral-silver mb-1">Sisa Budget Tersedia</p>
                            <p class="font-display font-bold text-[48px] tracking-[-1px] leading-[1.17] {{ $budgetRemaining < 0 ? 'text-semantic-red' : 'text-semantic-greentext' }}">
                                Rp{{ number_format($budgetRemaining, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Progress Bar (Sesuai FR-14) -->
                        <div class="mb-2 flex justify-between text-sm font-medium">
                            <span class="text-neutral-black">Terpakai: Rp{{ number_format($expense, 0, ',', '.') }}</span>
                            <span class="{{ $budgetPercentage >= 100 ? 'text-semantic-red' : 'text-brand' }} font-bold">{{ $budgetPercentage }}%</span>
                        </div>
                        <div class="w-full bg-neutral-border rounded-[9999px] h-4 mb-4 overflow-hidden relative">
                            <div class="{{ $budgetPercentage >= 100 ? 'bg-semantic-red' : 'bg-brand' }} h-4 rounded-[9999px]" @style(["width: {$budgetPercentage}%"])></div>
                        </div>
                        <div class="flex justify-between text-xs text-neutral-gray">
                            <span>Rp0</span>
                            <span>Total: Rp{{ number_format($totalBudget, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <p class="text-sm text-neutral-gray text-center py-8">
                            Kamu belum atur budget bulan ini. Isi form di sebelah kiri untuk mulai memantau pemakaian.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </main>

</body>
</html>