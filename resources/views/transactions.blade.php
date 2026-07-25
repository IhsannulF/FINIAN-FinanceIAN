<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi - FINIAN</title>

    <!-- Favicon FINIAN -->
    <link rel="icon" type="image/png" href="{{ asset('images/logofinian.png') }}">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-neutral-black" x-data="transactionModal('{{ route('transactions.store') }}')">

    @include('partials.navbar')

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-10">

        @if (session('success'))
            <div class="mb-6 rounded-[12px] bg-semantic-greenbg border border-[rgba(20,158,97,0.24)] text-semantic-greentext text-sm font-medium px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Page Header & Action -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h1 class="font-display font-bold text-[36px] tracking-[-0.5px]">Riwayat Transaksi</h1>
            <button
                type="button"
                @click="openCreate()"
                class="bg-brand text-white rounded-[12px] px-[16px] py-[13px] font-semibold text-base hover:bg-brand-dark transition shadow-micro flex items-center gap-2 w-fit"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Transaksi
            </button>
        </div>

        <!-- Filter -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
            <a href="{{ route('transactions') }}"
               class="rounded-[12px] px-4 py-2 text-sm font-medium {{ request('type') ? 'bg-[rgba(148,151,169,0.08)] text-neutral-black hover:bg-neutral-border transition' : 'bg-neutral-black text-white' }}">
                Semua
            </a>
            <a href="{{ route('transactions', ['type' => 'income']) }}"
               class="rounded-[12px] px-4 py-2 text-sm font-medium {{ request('type') === 'income' ? 'bg-neutral-black text-white' : 'bg-[rgba(148,151,169,0.08)] text-neutral-black hover:bg-neutral-border transition' }}">
                Pemasukan
            </a>
            <a href="{{ route('transactions', ['type' => 'expense']) }}"
               class="rounded-[12px] px-4 py-2 text-sm font-medium {{ request('type') === 'expense' ? 'bg-neutral-black text-white' : 'bg-[rgba(148,151,169,0.08)] text-neutral-black hover:bg-neutral-border transition' }}">
                Pengeluaran
            </a>
        </div>

        <!-- Transaction List -->
        <div class="rounded-[16px] border border-neutral-border bg-white shadow-whisper overflow-hidden">

            <!-- List Header -->
            <div class="grid grid-cols-12 gap-4 p-4 border-b border-neutral-border bg-neutral-50 text-xs font-semibold text-neutral-gray uppercase tracking-wider">
                <div class="col-span-3 md:col-span-2">Tanggal</div>
                <div class="col-span-6 md:col-span-4">Keterangan & Kategori</div>
                <div class="col-span-3 md:col-span-3 text-right md:text-left">Jumlah</div>
                <div class="hidden md:block col-span-3 text-right">Aksi</div>
            </div>

            @forelse ($transactions as $transaction)
                <div class="grid grid-cols-12 gap-4 p-4 items-center border-b border-neutral-border last:border-b-0 hover:bg-neutral-50 transition">
                    <div class="col-span-3 md:col-span-2">
                        <p class="text-sm font-medium text-neutral-black">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-neutral-silver">{{ $transaction->created_at->format('H:i') }}</p>
                    </div>
                    <div class="col-span-6 md:col-span-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-neutral-50 border border-neutral-border flex items-center justify-center text-[20px]"
                             @style(["color: " . ($transaction->category->color ?? '#7132f5')])>
                            <i class="{{ $transaction->category->icon ?? 'ph ph-sparkle' }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-black">{{ $transaction->description ?: $transaction->category->name }}</p>
                            @if ($transaction->type === 'income')
                                <span class="inline-block rounded-[6px] bg-semantic-greenbg px-2 py-0.5 text-[10px] font-semibold text-semantic-greentext mt-1">
                                    {{ $transaction->category->name }}
                                </span>
                            @else
                                <span class="inline-block rounded-[6px] bg-neutral-100 px-2 py-0.5 text-[10px] font-semibold text-neutral-gray mt-1">
                                    {{ $transaction->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-span-3 md:col-span-3 text-right md:text-left">
                        <p class="text-sm font-bold {{ $transaction->type === 'income' ? 'text-semantic-greentext' : 'text-neutral-black' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="col-span-12 md:col-span-3 flex justify-end gap-2 mt-3 md:mt-0">
                        <button
                            type="button"
                            @click='openEdit(@json($transaction))'
                            class="text-neutral-gray hover:text-brand text-sm font-medium px-3 py-1.5 border border-neutral-border rounded-[8px] hover:border-brand transition"
                        >
                            Edit
                        </button>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                              onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-neutral-gray hover:text-semantic-red text-sm font-medium px-3 py-1.5 border border-neutral-border rounded-[8px] hover:border-semantic-red transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-sm text-neutral-silver">
                    Belum ada transaksi. Klik "Tambah Transaksi" untuk mulai mencatat.
                </div>
            @endforelse
        </div>

        @if ($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </main>

    <!-- Modal Add/Edit Transaksi -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
        style="display: none;"
    >
        <div @click.outside="close()" class="w-full max-w-md rounded-[16px] bg-white border border-neutral-border shadow-whisper p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-display font-bold text-[22px]" x-text="mode === 'create' ? 'Tambah Transaksi' : 'Edit Transaksi'"></h2>
                <button type="button" @click="close()" class="text-neutral-gray hover:text-neutral-black">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-semibold text-neutral-gray uppercase tracking-wider mb-1.5">Tipe</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 rounded-[10px] border py-2.5 text-sm font-medium cursor-pointer"
                               :class="form.type === 'income' ? 'border-semantic-green bg-semantic-greenbg text-semantic-greentext' : 'border-neutral-border text-neutral-gray'">
                            <input type="radio" name="type" value="income" x-model="form.type" class="hidden">
                            Pemasukan
                        </label>
                        <label class="flex items-center justify-center gap-2 rounded-[10px] border py-2.5 text-sm font-medium cursor-pointer"
                               :class="form.type === 'expense' ? 'border-neutral-black bg-neutral-100 text-neutral-black' : 'border-neutral-border text-neutral-gray'">
                            <input type="radio" name="type" value="expense" x-model="form.type" class="hidden">
                            Pengeluaran
                        </label>
                    </div>
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-semibold text-neutral-gray uppercase tracking-wider mb-1.5">Kategori</label>
                    <div class="flex items-center gap-2">
                        <div class="shrink-0 w-10 h-10 rounded-[10px] bg-neutral-50 border border-neutral-border flex items-center justify-center text-[18px]"
                             :style="'color: ' + selectedCategoryColor()">
                            <i :class="selectedCategoryIcon()"></i>
                        </div>
                        <select id="category_id" name="category_id" x-model="form.category_id" required
                                class="w-full rounded-[10px] border border-neutral-border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-subtle focus:border-brand">
                            <option value="" disabled>Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-icon="{{ $category->icon ?? 'ph ph-sparkle' }}"
                                        data-color="{{ $category->color ?? '#7132f5' }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="amount" class="block text-xs font-semibold text-neutral-gray uppercase tracking-wider mb-1.5">Jumlah (Rp)</label>
                    <input type="number" id="amount" name="amount" x-model="form.amount" min="1" step="1" required
                           class="w-full rounded-[10px] border border-neutral-border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-subtle focus:border-brand">
                </div>

                <div>
                    <label for="transaction_date" class="block text-xs font-semibold text-neutral-gray uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" id="transaction_date" name="transaction_date" x-model="form.transaction_date" required
                           class="w-full rounded-[10px] border border-neutral-border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-subtle focus:border-brand">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-neutral-gray uppercase tracking-wider mb-1.5">Keterangan (opsional)</label>
                    <input type="text" id="description" name="description" x-model="form.description" maxlength="255"
                           class="w-full rounded-[10px] border border-neutral-border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-subtle focus:border-brand">
                </div>

                <button type="submit"
                        class="w-full bg-brand text-white rounded-[12px] px-[16px] py-[13px] font-semibold text-base hover:bg-brand-dark transition shadow-micro">
                    <span x-text="mode === 'create' ? 'Simpan Transaksi' : 'Update Transaksi'"></span>
                </button>
            </form>
        </div>
    </div>

</body>
</html>