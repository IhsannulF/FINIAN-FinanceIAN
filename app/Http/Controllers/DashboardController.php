<?php
namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\AiInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class DashboardController extends Controller
{
    public function index()
    {

        $userId = Auth::id();
        
        // Kita kunci ke bulan & tahun saat ini
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ambil total income, expense, dan balance bulan ini
        $summary = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount END), 0) as expense
            ")
            ->first();

        $income = $summary->income;
        $expense = $summary->expense;
        $balance = $income - $expense;

        // 2. Ambil total Budget bulan ini
        $budget = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $totalBudget = $budget ? $budget->total_budget : 0;
        $budgetRemaining = $totalBudget - $expense;
        
        // 3. Hitung Persentase Pemakaian Budget (maksimal 100%)
        $budgetPercentage = 0;
        if ($totalBudget > 0) {
            $budgetPercentage = min(100, round(($expense / $totalBudget) * 100));
        }

        // 4. Kelompokkan Pengeluaran Berdasarkan Kategori untuk Monitoring UI
        $expensesByCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->selectRaw('category_id, sum(amount) as total_amount, count(id) as total_trx')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        // 5. Ambil Insight dari AI
        $aiInsight = AiInsight::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        return view('dashboard', compact(
            'income', 'expense', 'balance', 
            'totalBudget', 'budgetRemaining', 'budgetPercentage', 
            'expensesByCategory', 'aiInsight'
        ));
    }

    public function generateInsight(Request $request)
    {
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Kumpulkan Data Keuangan Bulan Ini
        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        
        $budget = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        $totalBudget = $budget ? $budget->total_budget : 0;

        // Cari kategori pengeluaran terbesar
        $topCategory = $transactions->where('type', 'expense')
            ->groupBy('category.name')
            ->map(function ($row) {
                return $row->sum('amount');
            })
            ->sortDesc()
            ->keys()
            ->first() ?? 'Belum ada';

        // 2. Susun Prompt untuk AI
        $prompt = "Kamu adalah FINIAN, asisten keuangan cerdas. Berikan 1-2 kalimat (maksimal 30 kata) masukan keuangan langsung ke intinya untuk pengguna. 
        Konteks bulan ini: Pemasukan Rp" . number_format($income, 0, ',', '.') . 
        ", Pengeluaran Rp" . number_format($expense, 0, ',', '.') . 
        ", Batas Budget Rp" . number_format($totalBudget, 0, ',', '.') . 
        ". Pengeluaran terbesar di kategori: " . $topCategory . ". 
        Gunakan gaya bahasa santai tapi profesional, jangan gunakan poin-poin.";

        // 3. Panggil API Gemini (Gunakan gemini-3.5-flash yang didukung untuk Free Tier)
        $apiKey = trim(env('GEMINI_API_KEY'));
        
        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        // 4. Cek Jika Sukses
        if ($response->successful()) {
            $insightText = $response->json('candidates.0.content.parts.0.text');
            $insightText = trim($insightText, "\" \t\n\r\0\x0B");

            // Simpan ke Database
            AiInsight::updateOrCreate(
                [
                    'user_id' => $userId, 
                    'month' => $currentMonth, 
                    'year' => $currentYear
                ],
                ['content' => $insightText]
            );

            return redirect()->route('dashboard')->with('success', 'Analisa AI berhasil diperbarui!');
        } else {
            // MUNCULKAN ERROR JIKA API GAGAL
            dd('API GEMINI GAGAL:', $response->status(), $response->json());
        }
    }
    
}