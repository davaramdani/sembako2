@extends('layouts.app')

@section('content')
<div class="page-shell py-6 sm:py-8" x-data="paymentTimer('{{ $order->payment_expires_at?->toISOString() }}')">
    <a href="{{ route('user.orders') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-green-600"><i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Kembali ke pesanan</a>
    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i>{{ session('error') }}</div>
    @endif
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="section-title">Detail pesanan <span class="text-green-600">#{{ $order->id }}</span></h1><p class="mt-2 text-sm text-gray-500">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p></div>
        <span class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-gray-600 ring-1 ring-gray-200"><i class="fas {{ $order->statusDisplay['icon'] }} text-green-600" aria-hidden="true"></i>{{ $order->statusDisplay['text'] }}</span>
    </div>
    <div class="grid items-start gap-5 lg:grid-cols-3 lg:gap-6">
        <div class="min-w-0 space-y-5 lg:col-span-2">
            <section class="surface overflow-hidden" aria-labelledby="items-title">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 p-4 sm:px-6 sm:py-5">
                    <h2 id="items-title" class="font-bold text-gray-900"><i class="fas fa-basket-shopping mr-2 text-green-600" aria-hidden="true"></i>Produk pesanan</h2>
                    <span class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-500">{{ $order->items->count() }} produk</span>
                </div>
                <div class="divide-y divide-gray-100 px-4 sm:px-6">
                    @foreach($order->items as $item)
                        <div class="order-item flex items-start gap-3 py-5 sm:gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 text-gray-300 sm:h-20 sm:w-20">
                                @if($item->product && $item->product->photo_path)
                                    <img src="{{ asset('storage/' . $item->product->photo_path) }}" alt="{{ $item->product->name }}" class="h-full w-full object-contain" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                    <i class="fas fa-box hidden text-2xl" aria-hidden="true"></i>
                                @else
                                    <i class="fas fa-box text-2xl" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="flex min-w-0 flex-1 flex-col justify-between gap-2 sm:flex-row sm:gap-4">
                                <div class="min-w-0"><h3 class="text-sm font-semibold leading-6 text-gray-900">{{ $item->product->name ?? 'Produk tidak tersedia' }}</h3><p class="mt-1 text-xs leading-5 text-gray-500">{{ $item->quantity }} barang × Rp{{ number_format($item->price, 0, ',', '.') }}</p></div>
                                <p class="shrink-0 text-sm font-bold text-gray-900">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 bg-gray-50 px-4 py-5 sm:px-6"><span class="text-sm font-semibold text-gray-700">Total pembayaran</span><span class="text-xl font-bold text-green-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span></div>
            </section>
            @if($order->shipping_address)
                <section class="surface p-4 sm:p-6" aria-labelledby="shipping-title">
                    <h2 id="shipping-title" class="font-bold text-gray-900"><i class="fas fa-location-dot mr-2 text-green-600" aria-hidden="true"></i>Informasi pengiriman</h2>
                    <p class="mt-4 text-sm font-semibold text-gray-800">{{ $order->customer_name }}</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-500">{{ $order->shipping_address }}</p>
                </section>
            @endif
        </div>
        <div class="space-y-5 lg:sticky lg:top-36">
            @if($order->status == 'Menunggu Pembayaran')
                @if($order->payment_expires_at)
                    <div class="overflow-hidden rounded-2xl border border-amber-200 bg-amber-50 p-5" x-show="!expired">
                        <div class="flex items-center justify-between gap-3"><h2 class="text-sm font-semibold text-amber-900"><i class="fas fa-clock mr-1.5" aria-hidden="true"></i>Batas pembayaran</h2><span class="font-mono text-xl font-bold tabular-nums text-amber-700" x-text="timeLeft">--:--</span></div>
                        <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-amber-100"><div class="h-full rounded-full bg-amber-400 transition-all" :style="`width: ${progressPercentage}%`"></div></div>
                        <p class="mt-3 text-xs leading-5 text-amber-800">Selesaikan pembayaran sebelum waktu habis.</p>
                    </div>
                @endif
                <div class="surface p-6 text-center" x-show="expired" x-cloak>
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-xl text-gray-400"><i class="fas fa-hourglass-end" aria-hidden="true"></i></span>
                    <h2 class="mt-4 font-bold text-gray-900">Waktu pembayaran habis</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">Silakan buat pesanan baru untuk melanjutkan belanja.</p>
                    <a href="{{ route('shop.index') }}" class="btn-primary mt-5 w-full">Belanja lagi</a>
                </div>
                <section class="surface overflow-hidden" x-show="!expired" aria-labelledby="payment-title">
                    <div class="border-b border-gray-100 px-5 py-4"><h2 id="payment-title" class="font-bold text-gray-900">Pembayaran</h2><p class="mt-1 text-xs text-gray-500">Pilih metode pembayaran melalui Midtrans.</p></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-gray-500">Total pembayaran</span><span class="text-lg font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span></div>
                        <div class="mt-5 flex flex-wrap gap-2" aria-label="Metode pembayaran tersedia">
                            @foreach(['QRIS', 'GoPay', 'Transfer', 'Alfamart', '+lainnya'] as $method)
                                <span class="rounded-md border border-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-600">{{ $method }}</span>
                            @endforeach
                        </div>
                        @if($order->snap_token)
                            <button id="pay-button" type="button" class="btn-primary mt-6 w-full"><i class="fas fa-credit-card" aria-hidden="true"></i>Bayar sekarang</button>
                        @else
                            <div class="mt-5 rounded-xl border border-red-100 bg-red-50 p-3 text-sm leading-6 text-red-700" role="alert"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Token pembayaran tidak tersedia. Hubungi admin.</div>
                        @endif
                        <p class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400"><i class="fas fa-shield-alt text-green-600" aria-hidden="true"></i>Pembayaran diproses oleh Midtrans</p>
                    </div>
                </section>
            @else
                <section class="surface overflow-hidden" aria-labelledby="status-title">
                    <div class="border-b border-gray-100 px-5 py-4"><h2 id="status-title" class="font-bold text-gray-900">Status pesanan</h2></div>
                    <div class="p-5">
                        @php
                            $isCancelled = in_array($order->status, ['Dibatalkan', 'Expired']);
                        @endphp
                        <div class="flex items-center gap-3 rounded-xl p-4 {{ $isCancelled ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}"><i class="fas {{ $order->statusDisplay['icon'] }} text-lg" aria-hidden="true"></i><span class="text-sm font-bold">{{ $order->statusDisplay['text'] }}</span></div>
                        @if($order->status === 'Dikirim' && $order->tracking_message)
                            <div class="mt-5"><h3 class="text-sm font-semibold text-gray-800"><i class="fas fa-truck mr-2 text-green-600" aria-hidden="true"></i>Info pengiriman</h3><p class="mt-2 whitespace-pre-line rounded-xl bg-gray-50 p-4 text-sm leading-6 text-gray-600">{{ $order->tracking_message }}</p></div>
                        @endif
                        @if($order->payment_proof)
                            <div class="mt-5 border-t border-gray-100 pt-4"><h3 class="mb-2 text-sm font-semibold text-gray-800">Bukti pembayaran</h3><a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-semibold text-green-600 hover:text-green-700"><i class="fas fa-image" aria-hidden="true"></i>Lihat bukti pembayaran<i class="fas fa-external-link-alt text-xs" aria-hidden="true"></i></a></div>
                        @endif
                        @if($order->status === 'Dikirim')
                            <form action="{{ route('user.order.complete', $order) }}" method="POST" class="mt-5">
                                @csrf
                                <button type="submit" class="btn-primary w-full"><i class="fas fa-check-circle" aria-hidden="true"></i>Selesaikan pesanan</button>
                            </form>
                        @endif
                        @if($order->status === 'Selesai')
                            <a href="{{ route('user.invoice.download', $order) }}" class="btn-secondary mt-5 w-full"><i class="fas fa-download" aria-hidden="true"></i>Unduh bukti pembayaran</a>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>

<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
function paymentTimer(expiryTime) {
    return {
        expired: false,
        timeLeft: '--:--',
        progressPercentage: 100,
        interval: null,
        totalDuration: 0,
        init() {
            const expiryTimestamp = new Date(expiryTime).getTime();
            if (!expiryTime || !Number.isFinite(expiryTimestamp)) return;
            this.totalDuration = Math.max(1, expiryTimestamp - Date.now());
            const update = () => {
                const distance = Math.max(0, expiryTimestamp - Date.now());
                if (distance === 0) {
                    clearInterval(this.interval);
                    this.expired = true;
                    this.timeLeft = '00:00';
                    this.progressPercentage = 0;
                    return;
                }
                const minutes = Math.floor(distance / 60000);
                const seconds = Math.floor((distance % 60000) / 1000);
                this.timeLeft = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                this.progressPercentage = Math.min(100, Math.max(0, (distance / this.totalDuration) * 100));
            };
            this.interval = setInterval(update, 1000);
            update();
        },
        destroy() {
            clearInterval(this.interval);
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            snap.pay(@json($order->snap_token ?? ''), {
                onSuccess: function (result) {
                    window.location.href = '{{ route("user.orders") }}';
                },
                onPending: function (result) {
                    window.location.href = '{{ route("user.orders") }}';
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function () {}
            });
        });
    }
});
</script>
@endsection