@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@php
    $cart = session('cart', []);
    $total = array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $cart));
@endphp
<div class="bg-slate-50 py-6 sm:py-8">
    <div class="page-shell">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-xs text-slate-500 sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('shop.index') }}" class="transition hover:text-green-600">Beranda</a>
            <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
            <a href="{{ route('cart.index') }}" class="transition hover:text-green-600">Keranjang</a>
            <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
            <span class="text-slate-800" aria-current="page">Checkout</span>
        </nav>

        <div class="mb-6 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Checkout</h1>
                <p class="mt-2 text-sm text-slate-500">Pastikan detail pesanan dan alamat pengiriman sudah sesuai.</p>
            </div>
            <ol class="flex items-center gap-2 text-[11px] font-medium sm:gap-4 sm:text-xs" aria-label="Tahapan pemesanan">
                <li class="flex items-center gap-1.5 text-green-700"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100"><i class="fas fa-check text-[10px]" aria-hidden="true"></i></span>Keranjang</li>
                <li class="h-px w-3 bg-slate-200 sm:w-5" aria-hidden="true"></li>
                <li class="flex items-center gap-1.5 text-green-700" aria-current="step"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-600 text-white">2</span>Checkout</li>
                <li class="h-px w-3 bg-slate-200 sm:w-5" aria-hidden="true"></li>
                <li class="flex items-center gap-1.5 text-slate-400"><span class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 bg-white">3</span>Bayar</li>
            </ol>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('checkout.placeOrder') }}" method="POST" class="checkout-form">
            @csrf
            <div class="grid items-start gap-6 lg:grid-cols-3">
                <div class="space-y-5 lg:col-span-2">
                    <section class="surface p-5 sm:p-6" aria-labelledby="shipping-heading">
                        <h2 id="shipping-heading" class="flex items-center gap-3 text-base font-bold text-slate-900">
                            <i class="fas fa-map-marker-alt text-green-600" aria-hidden="true"></i>Alamat pengiriman
                        </h2>
                        @if(Auth::user()->address)
                            <div class="mt-5 rounded-xl border border-green-200 bg-green-50/50 p-4 sm:p-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                                    <span class="rounded bg-green-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-green-700">Utama</span>
                                </div>
                                <p class="mt-2 break-words text-sm leading-6 text-slate-600">{{ Auth::user()->address }}</p>
                            </div>
                        @else
                            <div class="mt-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">
                                <i class="fas fa-exclamation-circle mt-0.5 text-amber-600" aria-hidden="true"></i>
                                <div>
                                    <p class="font-semibold text-amber-800">Alamat belum lengkap</p>
                                    <p class="mt-1 leading-6 text-amber-700">Silakan lengkapi alamat pengiriman di profil Anda.</p>
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="surface p-5 sm:p-6" aria-labelledby="buyer-heading">
                        <h2 id="buyer-heading" class="flex items-center gap-3 text-base font-bold text-slate-900">
                            <i class="far fa-user text-green-600" aria-hidden="true"></i>Informasi pembeli
                        </h2>
                        <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-slate-500">Nama lengkap</dt>
                                <dd class="mt-1 text-sm font-medium text-slate-800">{{ Auth::user()->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Email</dt>
                                <dd class="mt-1 break-all text-sm font-medium text-slate-800">{{ Auth::user()->email }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="surface overflow-hidden" aria-labelledby="order-products-heading">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 id="order-products-heading" class="flex items-center gap-3 text-base font-bold text-slate-900"><i class="fas fa-shopping-basket text-green-600" aria-hidden="true"></i>Produk pesanan</h2>
                            <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-green-600 transition hover:text-green-700">Ubah</a>
                        </div>
                        @if(count($cart) > 0)
                            <div class="divide-y divide-slate-100">
                                @foreach($cart as $id => $details)
                                    <div class="flex items-start gap-4 p-5 sm:px-6">
                                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-100 bg-white sm:h-20 sm:w-20">
                                            <img src="{{ $details['photo_path'] ? asset('storage/' . $details['photo_path']) : asset('images/product-placeholder.svg') }}" alt="{{ $details['name'] }}" class="h-full w-full object-contain p-1" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="break-words text-sm font-medium leading-6 text-slate-800">{{ $details['name'] }}</h3>
                                            <p class="mt-1 text-xs text-slate-500">{{ $details['quantity'] }} × Rp{{ number_format($details['price'], 0, ',', '.') }}</p>
                                            <p class="mt-2 text-sm font-bold text-slate-900">Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-10 text-center">
                                <p class="text-sm text-slate-500">Keranjangmu masih kosong.</p>
                                <a href="{{ route('shop.index') }}" class="mt-3 inline-block text-sm font-semibold text-green-600">Mulai belanja</a>
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="surface p-5 sm:p-6 lg:sticky lg:top-36" aria-labelledby="checkout-summary-heading">
                    <h2 id="checkout-summary-heading" class="text-lg font-bold text-slate-900">Ringkasan belanja</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Total harga ({{ count($cart) }} produk)</dt>
                            <dd class="whitespace-nowrap font-medium text-slate-700">Rp{{ number_format($total, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Biaya pengiriman</dt>
                            <dd class="font-semibold text-green-600">Gratis</dd>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 pt-5">
                            <dt class="font-bold text-slate-900">Total pembayaran</dt>
                            <dd class="text-xl font-bold text-green-600">Rp{{ number_format($total, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    @if(count($cart) > 0)
                        <button type="submit" class="btn-place-order btn-primary mt-6 flex w-full items-center justify-center gap-2 disabled:cursor-wait disabled:opacity-60">
                            <span>Buat pesanan</span><i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </button>
                        <p class="mt-4 text-center text-xs leading-5 text-slate-500">Pilih metode pembayaran setelah pesanan dibuat.</p>
                    @endif
                    <a href="{{ route('cart.index') }}" class="mt-5 flex items-center justify-center gap-2 text-sm font-medium text-slate-500 transition hover:text-green-600">
                        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Kembali ke keranjang
                    </a>
                </aside>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.checkout-form');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        const button = form.querySelector('.btn-place-order');
        if (!button) return;
        if (button.disabled) {
            event.preventDefault();
            return;
        }
        button.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Memproses pesanan...</span>';
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
    });
});
</script>
@endsection
