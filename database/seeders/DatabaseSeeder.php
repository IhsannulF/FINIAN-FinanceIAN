<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed kategori default
        $this->call(CategorySeeder::class);

        // 2. Buat user dummy untuk testing
        $user = User::create([
            'name' => 'Muhammad Ihsanul Fikri',
            'email' => 'ihsanul@finian.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Buat beberapa transaksi dummy
        $foodCategory = Category::where('name', 'Food')->first();
        $transportCategory = Category::where('name', 'Transportation')->first();
        $shoppingCategory = Category::where('name', 'Shopping')->first();

        $user->transactions()->create([
            'category_id' => $foodCategory->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Makan siang',
            'transaction_date' => now(),
        ]);

        $user->transactions()->create([
            'category_id' => $transportCategory->id,
            'type' => 'expense',
            'amount' => 20000,
            'description' => 'Ojek online',
            'transaction_date' => now(),
        ]);

        $user->transactions()->create([
            'category_id' => $shoppingCategory->id,
            'type' => 'expense',
            'amount' => 150000,
            'description' => 'Beli baju',
            'transaction_date' => now(),
        ]);

        $user->transactions()->create([
            'category_id' => $foodCategory->id,
            'type' => 'income',
            'amount' => 1500000,
            'description' => 'Gaji freelance',
            'transaction_date' => now(),
        ]);

        // 4. Buat budget bulan ini
        $user->budgets()->create([
            'month' => now()->month,
            'year' => now()->year,
            'total_budget' => 1000000,
        ]);
    }
}