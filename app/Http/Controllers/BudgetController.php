<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * FR-13, FR-14: Tampilkan form budget bulan ini + status pemakaian.
     */
    public function index()
    {
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $budget = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $totalBudget = $budget->total_budget ?? 0;

        $expense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $budgetRemaining = $totalBudget - $expense;

        $budgetPercentage = 0;
        if ($totalBudget > 0) {
            $budgetPercentage = min(100, round(($expense / $totalBudget) * 100));
        }

        return view('budget', compact(
            'totalBudget', 'expense', 'budgetRemaining', 'budgetPercentage'
        ));
    }

    /**
     * FR-13: Simpan/update satu total budget untuk bulan berjalan.
     * updateOrCreate dipakai karena cuma ada 1 budget per user per bulan/tahun.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_amount' => ['required', 'numeric', 'min:0'],
        ]);

        Budget::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'month' => now()->month,
                'year' => now()->year,
            ],
            [
                'total_budget' => $validated['budget_amount'],
            ]
        );

        return redirect()
            ->route('budget')
            ->with('success', 'Budget bulan ini berhasil disimpan.');
    }
}