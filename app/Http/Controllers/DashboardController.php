<?php
namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\AiInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Kita kunci ke bulan & tahun saat ini
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ambil semua transaksi milik user yang login pada bulan ini
        $transactions = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->get();

        // 2. Hitung Pemasukan, Pengeluaran & Saldo Aktif
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // 3. Ambil total Budget bulan ini
        $budget = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $totalBudget = $budget ? $budget->total_budget : 0;
        $budgetRemaining = $totalBudget - $expense;
        
        // 4. Hitung Persentase Pemakaian Budget (maksimal 100%)
        $budgetPercentage = 0;
        if ($totalBudget > 0) {
            $budgetPercentage = min(100, round(($expense / $totalBudget) * 100));
        }

        // 5. Kelompokkan Pengeluaran Berdasarkan Kategori untuk Monitoring UI
        $expensesByCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->selectRaw('category_id, sum(amount) as total_amount, count(id) as total_trx')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        // 6. Ambil Insight dari AI
        $aiInsight = AiInsight::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        return view('dashboard', compact(
            'income', 'expense', 'balance', 
            'totalBudget', 'budgetRemaining', 'budgetPercentage', 
            'expensesByCategory','expensesByCategory', 'aiInsight'
        ));
    }
}