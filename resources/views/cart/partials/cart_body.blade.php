<nav class="mb-5 flex items-center gap-2 text-xs text-slate-500 sm:text-sm" aria-label="Breadcrumb">
    <a href="{{ route('shop.index') }}" class="transition hover:text-green-600">Beranda</a>
    <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
    <span class="text-slate-800" aria-current="page">Keranjang</span>
</nav>

<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Keranjang belanja</h1>
    <p class="mt-2 text-sm text-slate-500">Periksa kebutuhanmu sebelum lanjut ke pembayaran.</p>
</div>

@if(session('cart') && count(session('cart')) > 0)
    <div class="grid items-start gap-6 lg:grid-cols-3">
        <section class="surface overflow-hidden lg:col-span-2" aria-labelledby="cart-products-heading">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-6">
                <h2 id="cart-products-heading" class="flex items-center gap-3 font-bold text-slate-900">
                    <i class="fas fa-shopping-basket text-green-600" aria-hidden="true"></i>
                    Produk pilihanmu
                </h2>
                <span class="rounded-lg bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700"><span class="cart-count">{{ count(session('cart')) }}</span> produk</span>
            </div>

            <div class="divide-y divide-slate-100">
                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <article class="cart-item relative p-4 transition-colors hover:bg-slate-50/60 sm:p-6" data-cart-id="{{ $id }}">
                        <div class="flex items-start gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-100 bg-white sm:h-24 sm:w-24">
                                <img src="{{ $details['photo_path'] ? asset('storage/' . $details['photo_path']) : asset('images/product-placeholder.svg') }}" alt="{{ $details['name'] }}" class="h-full w-full object-contain p-1" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="item-name break-words text-sm font-semibold leading-6 text-slate-800 sm:text-base">{{ $details['name'] }}</h3>
                                <p class="mt-1 text-xs text-slate-500">Harga satuan</p>
                                <p class="item-price mt-0.5 text-base font-bold text-slate-900">Rp{{ number_format($details['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="hidden text-right sm:block">
                                <p class="text-xs text-slate-500">Subtotal</p>
                                <p class="subtotal-amount mt-1 whitespace-nowrap text-base font-bold text-slate-900">Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 sm:pl-28">
                            <form action="{{ route('cart.remove', $id) }}" method="POST" class="remove-form">
                                @csrf
                                <button type="submit" class="remove-btn inline-flex min-h-10 items-center gap-2 rounded-lg px-2 text-sm font-medium text-slate-500 transition hover:bg-red-50 hover:text-red-600" aria-label="Hapus {{ $details['name'] }} dari keranjang">
                                    <i class="far fa-trash-alt" aria-hidden="true"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="quantity-update-form flex items-center gap-2">
                                @csrf
                                <label for="quantity-{{ $id }}" class="text-xs text-slate-500">Jumlah</label>
                                <input id="quantity-{{ $id }}" type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" step="1" required inputmode="numeric" class="field-input h-10 w-16 rounded-lg px-2 py-2 text-center text-sm font-semibold" aria-label="Jumlah {{ $details['name'] }}">
                                <button type="submit" class="min-h-10 rounded-lg border border-green-200 px-3 text-xs font-semibold text-green-700 transition hover:bg-green-50">Simpan</button>
                            </form>
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-sm sm:hidden">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="subtotal-amount font-bold text-slate-900">Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="surface p-5 sm:p-6 lg:sticky lg:top-36" aria-labelledby="cart-summary-heading">
            <h2 id="cart-summary-heading" class="text-lg font-bold text-slate-900">Ringkasan belanja</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Jumlah produk</dt>
                    <dd class="cart-item-count font-medium text-slate-700">{{ count(session('cart')) }} produk</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Subtotal</dt>
                    <dd class="cart-subtotal font-medium text-slate-700">Rp{{ number_format($total, 0, ',', '.') }}</dd>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 pt-5">
                    <dt class="font-bold text-slate-900">Total belanja</dt>
                    <dd class="cart-total text-xl font-bold text-green-600">Rp{{ number_format($total, 0, ',', '.') }}</dd>
                </div>
            </dl>
            <a href="{{ route('checkout.index') }}" class="btn-checkout btn-primary mt-6 flex w-full items-center justify-center gap-2">
                Lanjut ke checkout <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
            <a href="{{ route('shop.index') }}" class="btn-secondary mt-3 flex w-full items-center justify-center gap-2">
                Lanjut belanja
            </a>
            <p class="mt-4 text-center text-xs leading-5 text-slate-400">Periksa jumlah produk sebelum melanjutkan.</p>
        </aside>
    </div>
@else
    <div class="surface flex min-h-[360px] items-center justify-center px-6 py-14 sm:min-h-[420px]">
        <div class="max-w-md text-center">
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-green-50 text-4xl text-green-600">
                <i class="fas fa-shopping-basket" aria-hidden="true"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Keranjangmu masih kosong</h2>
            <p class="mt-3 text-sm leading-6 text-slate-500">Yuk, pilih kebutuhan sehari-hari dan tambahkan produk favoritmu ke keranjang.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary mt-7 inline-flex items-center gap-2">Mulai belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
        </div>
    </div>
@endif
