<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada
use Dompdf\Dompdf; // Pastikan ini ada

class UserOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan aktif milik pengguna.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Expired'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan riwayat pesanan (yang sudah selesai atau dibatalkan).
     */
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['Selesai', 'Dibatalkan', 'Expired'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.history', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        // Pastikan hanya pemilik pesanan yang bisa melihat detailnya
        if ($order->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Memproses pembayaran yang diunggah oleh pengguna.
     */
    public function pay(Request $request, Order $order)
    {
        // Validasi
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus bukti pembayaran lama jika ada
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Simpan file baru
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Update status pesanan
        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'paid',
            'status' => 'Dibayar', // Status diubah menjadi 'Dibayar' menunggu konfirmasi admin
        ]);

        return redirect()->route('user.order.show', $order)->with('success', 'Bukti pembayaran berhasil diunggah. Pesanan Anda akan segera diproses.');
    }

    /**
     * Menandai pesanan sebagai selesai oleh pengguna.
     * FUNGSI BARU ADA DI SINI.
     */
    public function complete(Order $order)
    {
        // Pastikan hanya pemilik pesanan yang bisa menyelesaikan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan untuk melakukan tindakan ini.');
        }

        // Hanya pesanan yang statusnya 'Dikirim' yang bisa diselesaikan
        if ($order->status !== 'Dikirim') {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat diselesaikan.');
        }

        $order->update(['status' => 'Selesai']);

        return redirect()->route('user.orders')->with('success', 'Pesanan #' . $order->id . ' telah ditandai sebagai selesai. Terima kasih telah berbelanja!');
    }


    /**
     * Mengunduh invoice dalam format PDF.
     */
    public function downloadInvoice(Order $order)
    {
        // Pastikan hanya pemilik yang bisa download
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Render view ke HTML
        $html = view('orders.invoice', compact('order'))->render();

        // Buat objek Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Opsional) Atur ukuran kertas dan orientasi
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output PDF ke browser untuk diunduh
        return $dompdf->stream('invoice-' . $order->id . '.pdf');
    }
}