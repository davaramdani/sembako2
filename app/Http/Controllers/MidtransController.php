<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    /**
     * Handle webhook notifikasi dari Midtrans.
     * Endpoint ini TIDAK pakai middleware auth & csrf.
     */
    public function notification(Request $request)
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $paymentType       = $notification->payment_type;
            $orderId           = $notification->order_id; // Format: ORDER-{id}
            $fraudStatus       = $notification->fraud_status;

            // Ekstrak ID order dari string "ORDER-33" → 33
            $orderRealId = str_replace('ORDER-', '', $orderId);
            $order = Order::find($orderRealId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Format payment_type jadi label yang readable
            $paymentLabel = match($paymentType) {
                'qris'         => 'QRIS',
                'bank_transfer'=> 'bank_transfer',
                'cstore'       => $notification->store === 'alfamart' ? 'alfamart' : 'indomaret',
                'credit_card'  => 'credit_card',
                'gopay'        => 'QRIS',
                'shopeepay'    => 'QRIS',
                default        => $paymentType,
            };

            // Logika update status berdasarkan notifikasi Midtrans
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status'         => 'Dibayar',
                        'payment_method' => $paymentLabel,
                    ]);

                    // Update payment_method di stock_movements
                    StockMovement::where('note', 'Pesanan #' . $order->id)
                        ->whereNull('payment_method')
                        ->update(['payment_method' => $paymentLabel]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'Dibayar',
                    'payment_method' => $paymentLabel,
                ]);

                // Update payment_method di stock_movements
                StockMovement::where('note', 'Pesanan #' . $order->id)
                    ->whereNull('payment_method')
                    ->update(['payment_method' => $paymentLabel]);

            } elseif ($transactionStatus == 'pending') {
                $order->update(['payment_status' => 'pending']);

            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update([
                    'payment_status' => 'failed',
                    'status'         => 'Dibatalkan',
                ]);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}