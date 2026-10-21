@extends('layouts.app')

@section('content')
<div class="page-shell py-6 sm:py-8">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-medium text-gray-400">Akun saya / Pesanan</p>
            <h1 class="section-title">Pesanan saya</h1>
            <p class="mt-2 text-sm text-gray-500">Pantau pembayaran dan perjalanan belanja Anda.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="hidden items-center gap-2 text-sm font-semibold text-green-600 hover:text-green-700 sm:inline-flex">Lanjut belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
    </div>
    <nav class="mb-6 flex gap-6 border-b border-gray-200" aria-label="Daftar pesanan">
        <a href="{{ route('user.orders') }}" aria-current="page" class="flex items-center gap-2 border-b-2 border-green-600 px-1 pb-4 text-sm font-semibold text-green-600">
            Pesanan aktif
            @if($orders->count() > 0)<span class="rounded-md bg-green-100 px-2 py-0.5 text-xs">{{ $orders->count() }}</span>@endif
        </a>
        <a href="{{ route('user.history') }}" class="border-b-2 border-transparent px-1 pb-4 text-sm font-semibold text-gray-500 transition hover:text-green-600">Riwayat pesanan</a>
    </nav>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i>{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="surface px-6 py-16 text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-50 text-3xl text-green-600"><i class="fas fa-basket-shopping" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold text-gray-900">Belum ada pesanan aktif</h2>
            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500">Pesanan yang sedang menunggu pembayaran atau pengiriman akan muncul di sini.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary mt-6">Mulai belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $statusClasses = [
                        'warning' => 'bg-amber-50 text-amber-700',
                        'info' => 'bg-sky-50 text-sky-700',
                        'primary' => 'bg-violet-50 text-violet-700',
                        'success' => 'bg-green-50 text-green-700',
                        'danger' => 'bg-red-50 text-red-700',
                        'secondary' => 'bg-gray-100 text-gray-600',
                    ];
                @endphp
                <article class="order-card surface overflow-hidden" data-created="{{ $order->created_at->toISOString() }}">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4 sm:px-6">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                            <h2 class="order-id flex items-center gap-2 text-sm font-bold text-gray-900"><i class="fas fa-receipt text-green-600" aria-hidden="true"></i> Pesanan #{{ $order->id }}</h2>
                            <span class="order-date text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-semibold {{ $statusClasses[$order->statusDisplay['color']] ?? $statusClasses['secondary'] }}"><i class="fas {{ $order->statusDisplay['icon'] }}" aria-hidden="true"></i>{{ $order->statusDisplay['text'] }}</span>
                    </div>
                    @if($order->status === 'Menunggu Pembayaran' && $order->payment_expires_at)
                        <div class="payment-timer-section border-b border-amber-100 bg-amber-50 px-4 py-3 sm:px-6" data-expires="{{ $order->payment_expires_at->toISOString() }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-amber-800"><span><i class="fas fa-clock mr-1.5" aria-hidden="true"></i>Selesaikan pembayaran dalam</span><span class="font-bold tabular-nums" id="timer-{{ $order->id }}">Menghitung waktu...</span></div>
                            <div class="mt-2 h-1 overflow-hidden rounded-full bg-amber-100"><div class="h-full rounded-full bg-amber-400 transition-all" id="progress-{{ $order->id }}"></div></div>
                        </div>
                    @endif
                    <div class="grid gap-5 p-4 sm:p-6 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="min-w-0 space-y-3">
                            @foreach($order->items->take(2) as $item)
                                <div class="flex items-center gap-3">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-gray-50 text-gray-300">
                                        @if($item->product && $item->product->photo_path)
                                            <img src="{{ asset('storage/' . $item->product->photo_path) }}" alt="{{ $item->product->name }}" class="h-full w-full object-contain" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                            <i class="fas fa-box hidden text-xl" aria-hidden="true"></i>
                                        @else
                                            <i class="fas fa-box text-xl" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0"><h3 class="text-sm font-semibold text-gray-800">{{ $item->product->name ?? 'Produk' }}</h3><p class="mt-1 text-xs text-gray-500">{{ $item->quantity }} barang × Rp{{ number_format($item->price, 0, ',', '.') }}</p></div>
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)<p class="text-xs text-gray-500">+{{ $order->items->count() - 2 }} produk lainnya</p>@endif
                        </div>
                        <div class="border-t border-gray-100 pt-4 md:min-w-48 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                            <p class="text-xs text-gray-500">Total pembayaran</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $order->items->count() }} produk</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 px-4 py-4 sm:px-6">
                        <a href="{{ route('user.order.show', $order) }}" class="btn-secondary flex-1 sm:flex-none">Lihat detail</a>
                        @if($order->status === 'Menunggu Pembayaran')
                            <a href="{{ route('user.order.show', $order) }}" class="btn-primary flex-1 sm:flex-none">Bayar sekarang</a>
                        @endif
                        @if($order->status === 'Dikirim')
                            <form action="{{ route('user.order.complete', $order) }}" method="POST" class="complete-form w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="btn-primary w-full"><i class="fas fa-check-circle" aria-hidden="true"></i>Selesaikan pesanan</button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.payment-timer-section').forEach(timerSection => {
        const orderCard = timerSection.closest('.order-card');
        const orderId = orderCard.querySelector('.order-id').textContent.match(/\d+/)[0];
        const timerElement = document.getElementById(`timer-${orderId}`);
        const progressElement = document.getElementById(`progress-${orderId}`);
        const targetTime = new Date(timerSection.dataset.expires).getTime();
        const createdTime = new Date(orderCard.dataset.created).getTime();
        const totalDuration = Math.max(1, targetTime - createdTime);
        if (!timerElement || !Number.isFinite(targetTime)) return;
        let interval;
        function updateTimer() {
            const distance = Math.max(0, targetTime - Date.now());
            if (distance === 0) {
                clearInterval(interval);
                timerElement.textContent = 'Waktu habis';
                orderCard.style.opacity = '0.5';
            } else {
                const hours = Math.floor(distance / 3600000);
                const minutes = Math.floor((distance % 3600000) / 60000);
                const seconds = Math.floor((distance % 60000) / 1000);
                timerElement.textContent = `${hours}j ${minutes}m ${seconds}s`;
            }
            if (progressElement) progressElement.style.width = `${Math.min(100, (distance / totalDuration) * 100)}%`;
        }
        interval = setInterval(updateTimer, 1000);
        updateTimer();
    });
});
</script>
@endsection