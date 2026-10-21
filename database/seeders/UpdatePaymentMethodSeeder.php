<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class UpdatePaymentMethodSeeder extends Seeder
{
    public function run()
    {
        // Daftar metode pembayaran yang mungkin
        $paymentMethods = ['cash', 'credit_card', 'bank_transfer'];

        // Ambil semua StockMovement dengan payment_method null
        $movements = StockMovement::whereNull('payment_method')->get();

        // Log jumlah data yang akan diperbarui
        \Log::info('Updating ' . $movements->count() . ' stock movements with payment methods.');

        // Perbarui setiap entri dengan metode pembayaran acak
        foreach ($movements as $movement) {
            $movement->update([
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
            ]);
        }

        \Log::info('Successfully updated payment methods for stock movements.');
    }
}