@props(['product'])
<article class="product-card group">
    <div class="relative aspect-square overflow-hidden bg-[#fafbf9] p-4">
        <img src="{{ $product->photo_path ? asset('storage/' . $product->photo_path) : asset('images/product-placeholder.svg') }}"
             alt="{{ $product->name }}" loading="lazy" width="320" height="320"
             onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}'"
             class="h-full w-full object-contain mix-blend-multiply transition duration-300 group-hover:scale-105">
        @if($product->current_stock < 10 && $product->current_stock > 0)
            <span class="absolute left-2 top-2 rounded-md bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-800">Stok terbatas</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <h3 class="line-clamp-2 min-h-[40px] text-sm font-medium leading-5">{{ $product->name }}</h3>
        <p class="mt-2 text-base font-extrabold text-slate-900 sm:text-lg">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
        <div class="mb-4 mt-2 flex items-center gap-1.5 text-[10px] text-slate-500 sm:text-xs">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $product->current_stock > 0 ? 'bg-green-500' : 'bg-slate-300' }}"></span>
            {{ $product->current_stock > 0 ? 'Stok ' . $product->current_stock . ' tersedia' : 'Stok habis' }}
        </div>
        @auth
            <form action="{{ route('cart.add.ajax', $product->id_product) }}" method="POST" class="add-to-cart-form mt-auto">
                @csrf
                @if($product->current_stock > 0)
                    <div class="flex gap-1.5">
                        <label for="quantity-{{ $product->id_product }}" class="sr-only">Jumlah {{ $product->name }}</label>
                        <input id="quantity-{{ $product->id_product }}" type="number" name="quantity" value="1" min="1" max="{{ $product->current_stock }}" required class="w-11 min-w-0 rounded-lg border border-slate-200 px-1 py-2 text-center text-xs sm:w-12">
                        <button type="submit" class="flex min-h-[38px] min-w-0 flex-1 items-center justify-center gap-1.5 rounded-lg border border-green-600 px-1.5 py-2 text-xs font-bold text-green-700 hover:bg-green-600 hover:text-white disabled:opacity-50"><i class="fas fa-plus text-[10px]" aria-hidden="true"></i> Keranjang</button>
                    </div>
                @else
                    <button type="button" disabled class="w-full rounded-lg bg-slate-100 px-3 py-2.5 text-xs font-semibold text-slate-400">Stok habis</button>
                @endif
            </form>
        @endauth
        @guest
            <a href="{{ route('login') }}" class="mt-auto flex min-h-[38px] items-center justify-center gap-2 rounded-lg border border-green-600 py-2 text-xs font-bold text-green-700 hover:bg-green-600 hover:text-white"><i class="fas fa-plus text-[10px]" aria-hidden="true"></i> Belanja</a>
        @endguest
    </div>
</article>
