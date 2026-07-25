<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
        public function index(Request $request)
        {
            $transactions = Transaction::with('category')
        ->where('user_id', Auth::id())
        ->when(request('type'), function ($query) {
            return $query->where('type', request('type'));
        })
        ->latest()
        ->paginate(5) 
        ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('transactions', compact('transactions', 'categories'));
    }

    /**
     * FR-08, FR-09: Tambah transaksi baru (income/expense).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()
            ->route('transactions')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * FR-10: Edit transaksi milik user yang login.
     */
    public function update(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        $transaction->update($validated);

        return redirect()
            ->route('transactions')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * FR-11: Hapus transaksi milik user yang login.
     */
    public function destroy(Transaction $transaction)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $transaction->delete();

        return redirect()
            ->route('transactions')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}