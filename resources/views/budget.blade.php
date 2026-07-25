<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budget Bulanan - FINIAN</title>
    
</head>
<body class="antialiased">

    <!-- Header Navigation -->
    <header class="border-b border-neutral-border">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logofinian.png') }}" alt="FINIAN" class="h-8 w-auto">
                <span class="font-display font-bold text-lg">FINIAN</span>
            </div>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-neutral-gray">
                <a href="/dashboard" class="hover:text-neutral-black">Dashboard</a>
                <a href="/transactions" class="hover:text-neutral-black">Transaksi</a>
                <a href="/budget" class="text-neutral-black">Budget</a>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-subtle text-brand flex items-center justify-center font-bold text-sm">
                    AP
                </div>
                <span class="text-sm font-medium">Andi Pratama</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-10">
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="font-display font-bold text-[36px] tracking-[-0.5px]">Budget Bulanan</h1>
            <p class="text-neutral-gray mt-2 text-base">Atur batas pengeluaranmu untuk bulan <span class="font-semibold text-neutral-black">Juli 2026</span>.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Kolom Kiri: Form Input Budget -->
            <div>
                <div class="rounded-[16px] border border-neutral-border bg-white shadow-whisper p-6">
                    <h2 class="font-display font-semibold text-[22px] mb-6">Atur Total Budget</h2>
                    
                    <form action="#" method="POST">
                        <!-- Token CSRF untuk Laravel (aktifkan saat integrasi backend) -->
                        <!-- @csrf -->
                        
                        <div class="mb-6">
                            <label for="budget_amount" class="block text-sm font-medium text-neutral-black mb-2">Total Budget (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-neutral-gray font-medium">Rp</span>
                                </div>
                                <input type="number" name="budget_amount" id="budget_amount" value="2000000" 
                                    class="block w-full pl-12 pr-4 py-4 border border-neutral-border rounded-[12px] text-lg font-semibold text-neutral-black focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition"
                                    placeholder="0" required>
                            </div>
                            <p class="text-xs text-neutral-silver mt-2">Masukkan satu angka total batas pengeluaran bulan ini.</p>
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
                    
                    <!-- Angka Utama -->
                    <div class="mb-6">
                        <p class="text-sm font-medium text-neutral-silver mb-1">Sisa Budget Tersedia</p>
                        <p class="font-display font-bold text-[48px] tracking-[-1px] leading-[1.17] text-semantic-greentext">Rp1.480.000</p>
                    </div>

                    <!-- Progress Bar (Sesuai FR-14) -->
                    <div class="mb-2 flex justify-between text-sm font-medium">
                        <span class="text-neutral-black">Terpakai: Rp520.000</span>
                        <span class="text-brand font-bold">26%</span>
                    </div>
                    <div class="w-full bg-neutral-border rounded-[9999px] h-4 mb-4 overflow-hidden relative">
                        <!-- Bar Progress -->
                        <div class="bg-brand h-4 rounded-[9999px]" style="width: 26%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-neutral-gray">
                        <span>Rp0</span>
                        <span>Total: Rp2.000.000</span>
                    </div>

                </div>
            </div>

        </div>
    </main>

</body>
</html>