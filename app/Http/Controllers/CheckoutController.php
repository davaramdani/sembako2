<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = session()->get('cart');

        if (!auth()->user()->address) {
            return redirect()->route('shop.index')->with('error', 'Harap lengkapi alamat Anda terlebih dahulu untuk melanjutkan checkout.');
        }

        if (!$cart || count($cart) == 0) {
            return redirect()->route('shop.index')->with('error', 'Keranjang Anda kosong untuk melakukan checkout.');
        }

        return view('checkout.index');
    }

    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart');
        if (!$cart) {
            return redirect()->route('shop.index')->with('error', 'Keranjang Anda kosong.');
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($cart as $details) {
                $totalPrice += $details['price'] * $details['quantity'];
            }

            $order = Order::create([
                'user_id'            => $user->id_user,
                'total_price'        => $totalPrice,
                'status'             => 'Menunggu Pembayaran',
                'payment_status'     => 'pending',
                'payment_expires_at' => now()->addMinutes(60),
                'customer_name'      => $user->name,
                'customer_email'     => $user->email,
                'shipping_address'   => $user->address,
            ]);

            $itemDetails = [];
            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $id,
                    'quantity'   => $details['quantity'],
                    'price'      => $details['price'],
                ]);

                // Catat StockMovement — booted event di model otomatis potong stok
                // Jangan tambahkan pengurangan stok manual di sini agar tidak double
                StockMovement::create([
                    'product_id'     => $id,
                    'movement_type'  => 'out',
                    'quantity'       => $details['quantity'],
                    'price_at_time'  => $details['price'],
                    'payment_method' => null, 
                    'note'           => 'Pesanan #' . $order->id,
                    'movement_date'  => now(),
                ]);

                $itemDetails[] = [
                    'id'       => $id,
                    'price'    => (int) $details['price'],
                    'quantity' => $details['quantity'],
                    'name'     => substr($details['name'], 0, 50),
                ];
            }

            // ── Generate Midtrans Snap Token ──────────────────────
            Config::$serverKey    = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => 'ORDER-' . $order->id,
                    'gross_amount' => (int) $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => '-',
                    'address'    => $user->address,
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
            // ─────────────────────────────────────────────────────

            DB::commit();
            session()->forget('cart');

            return redirect()->route('user.order.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}