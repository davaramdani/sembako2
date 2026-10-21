@extends('layouts.app')

@section('content')
<div class="page-shell py-6 sm:py-8">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-medium text-gray-400">Akun saya / Pesanan</p>
            <h1 class="section-title">Riwayat pesanan</h1>
            <p class="mt-2 text-sm text-gray-500">Semua belanja yang sudah selesai, tersimpan di sini.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="hidden items-center gap-2 text-sm font-semibold text-green-600 hover:text-green-700 sm:inline-flex">Lanjut belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
    </div>
    <nav class="mb-6 flex gap-6 border-b border-gray-200" aria-label="Daftar pesanan">
        <a href="{{ route('user.orders') }}" class="border-b-2 border-transparent px-1 pb-4 text-sm font-semibold text-gray-500 transition hover:text-green-600">Pesanan aktif</a>
        <a href="{{ route('user.history') }}" aria-current="page" class="border-b-2 border-green-600 px-1 pb-4 text-sm font-semibold text-green-600">Riwayat pesanan</a>
    </nav>
    @if(session('success'))
        <div class="notification-success mb-5 flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">
            <span><i class="fas fa-check-circle mr-2" aria-hidden="true"></i>{{ session('success') }}</span>
            <button type="button" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg hover:bg-green-100" aria-label="Tutup pemberitahuan" onclick="this.parentElement.remove()"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
    @endif
    @if($orders->isEmpty())
        <div class="surface px-6 py-16 text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-50 text-3xl text-green-600"><i class="fas fa-receipt" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold text-gray-900">Belum ada riwayat pesanan</h2>
            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500">Pesanan yang sudah selesai dan diterima akan muncul di sini. Yuk, temukan kebutuhan Anda.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary mt-6">Mulai belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
        </div>
    @else
        <div class="grid gap-5 lg:grid-cols-2">
            @foreach($orders as $order)
                <article class="history-card surface flex flex-col overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-4 sm:px-6">
                        <div><h2 class="flex items-center gap-2 text-sm font-bold text-gray-900"><i class="fas fa-receipt text-green-600" aria-hidden="true"></i>Pesanan #{{ $order->id }}</h2><p class="mt-1.5 text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p></div>
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-green-50 px-2.5 py-1.5 text-xs font-semibold text-green-700"><i class="fas fa-check-circle" aria-hidden="true"></i>{{ $order->status }}</span>
                    </div>
                    <div class="flex-1 p-4 sm:p-6">
                        <p class="text-xs text-gray-500">Total yang dibayar</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                        <div class="mt-5 rounded-xl bg-gray-50 px-3 py-4 sm:px-4">
                            <div class="flex items-center justify-between text-green-600" aria-hidden="true">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100"><i class="fas fa-shopping-bag text-xs"></i></span><span class="mx-2 h-px flex-1 bg-green-200"></span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100"><i class="fas fa-credit-card text-xs"></i></span><span class="mx-2 h-px flex-1 bg-green-200"></span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100"><i class="fas fa-truck text-xs"></i></span><span class="mx-2 h-px flex-1 bg-green-200"></span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-600 text-white"><i class="fas fa-check text-xs"></i></span>
                            </div>
                            <div class="mt-2 grid grid-cols-4 gap-2 text-xs font-medium text-gray-500"><span>Dibuat</span><span class="text-center">Dibayar</span><span class="text-center">Dikirim</span><span class="text-right text-green-700">Selesai</span></div>
                        </div>
                        <p class="mt-4 flex items-center gap-2 text-xs text-gray-500"><i class="fas fa-check-circle text-green-600" aria-hidden="true"></i>Diterima pada {{ $order->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 p-4 sm:px-6">
                        <a href="{{ route('user.order.show', $order) }}" class="text-sm font-semibold text-green-600 hover:text-green-700">Lihat detail</a>
                        <a href="{{ route('user.invoice.download', $order) }}" class="btn-secondary"><i class="fas fa-download" aria-hidden="true"></i>Unduh bukti pembayaran</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        document.querySelectorAll('.notification-success').forEach(notification => {
            notification.style.transition = 'opacity 0.3s ease';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        });
    }, 5000);
});
</script>
@endsection