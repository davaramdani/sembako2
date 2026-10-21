@php
    use App\Models\Product;
    use App\Models\Order;

    $lowStockProducts = Product::where('current_stock', '<', 10)
        ->orderBy('current_stock', 'asc')
        ->take(5)
        ->get();

   $newOrders = Order::whereIn('status', [
        'Menunggu Pembayaran',
        'Dibayar',
        'Diproses',
        'Dikirim',
    ])
    ->latest()
    ->take(5)
    ->get();

    $totalNotifications = $lowStockProducts->count() + $newOrders->count();
@endphp

<div x-data="{ open: false, tab: 'all' }" class="relative">
    <button
        type="button"
        @click="open = !open"
        class="relative flex h-10 w-10 items-center justify-center rounded-full text-gray-500 outline-none transition hover:bg-gray-100 focus:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:bg-gray-700"
        title="Notifikasi"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
        </svg>

        @if ($totalNotifications > 0)
            <span
                class="absolute flex items-center justify-center rounded-full bg-danger-600 text-[10px] font-bold text-white shadow"
                style="top: -4px; right: -4px; min-width: 20px; height: 20px; padding: 0 5px;"
            >
                {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
            </span>
        @endif
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition.origin.top.right
        @click.away="open = false"
        class="absolute right-0 z-50 mt-3 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800"
        style="display: none; width: 420px;"
    >
        <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-700">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                        Notifikasi Admin
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                        Pantau order baru dan stok produk yang menipis
                    </p>
                </div>

                @if ($totalNotifications > 0)
                    <span class="rounded-full bg-primary-50 px-2 py-1 text-xs font-semibold text-primary-600 dark:bg-gray-700 dark:text-gray-200">
                        {{ $totalNotifications }}
                    </span>
                @endif
            </div>

            <div class="mt-4 flex rounded-lg bg-gray-100 p-1 dark:bg-gray-900">
                <button
                    type="button"
                    @click="tab = 'all'"
                    class="flex-1 rounded-md px-3 py-2 text-xs font-medium transition"
                    :class="tab === 'all'
                        ? 'bg-white text-primary-600 shadow dark:bg-gray-800 dark:text-white'
                        : 'text-gray-500 dark:text-gray-400'"
                >
                    Semua
                </button>

                <button
                    type="button"
                    @click="tab = 'orders'"
                    class="flex-1 rounded-md px-3 py-2 text-xs font-medium transition"
                    :class="tab === 'orders'
                        ? 'bg-white text-primary-600 shadow dark:bg-gray-800 dark:text-white'
                        : 'text-gray-500 dark:text-gray-400'"
                >
                    Order
                </button>

                <button
                    type="button"
                    @click="tab = 'stock'"
                    class="flex-1 rounded-md px-3 py-2 text-xs font-medium transition"
                    :class="tab === 'stock'
                        ? 'bg-white text-primary-600 shadow dark:bg-gray-800 dark:text-white'
                        : 'text-gray-500 dark:text-gray-400'"
                >
                    Stok
                </button>
            </div>
        </div>

        <div style="max-height: 420px; overflow-y: auto;">
            <div x-show="tab === 'all'">
                @foreach ($newOrders as $order)
                    <a
                        href="{{ \App\Filament\Resources\OrderResource::getUrl('view', ['record' => $order]) }}"
                        class="flex items-start gap-3 border-b border-gray-100 px-4 py-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/40"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.293 1.293A1 1 0 006.414 16H19m-9 4a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    Order baru masuk
                                </p>
                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-medium text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                    Order
                                </span>
                            </div>
                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-300">
                                #{{ $order->id }} • {{ $order->customer_name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-400">
                                Total Rp{{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @endforeach

                @foreach ($lowStockProducts as $product)
                    <a
                        href="{{ \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $product->id_product]) }}"
                        class="flex items-start gap-3 border-b border-gray-100 px-4 py-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/40"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 3.75h.008v.008H12v-.008z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    Stok produk menipis
                                </p>
                                <span class="rounded-full bg-yellow-50 px-2 py-0.5 text-[10px] font-medium text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400">
                                    Stok
                                </span>
                            </div>
                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-300">
                                {{ $product->name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-400">
                                Sisa stok {{ $product->current_stock }}
                            </p>
                        </div>
                    </a>
                @endforeach

                @if ($totalNotifications === 0)
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada notifikasi
                    </div>
                @endif
            </div>

            <div x-show="tab === 'orders'">
                @forelse ($newOrders as $order)
                    <a
                        href="{{ \App\Filament\Resources\OrderResource::getUrl('view', ['record' => $order]) }}"
                        class="flex items-start gap-3 border-b border-gray-100 px-4 py-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/40"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.293 1.293A1 1 0 006.414 16H19m-9 4a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                Order baru masuk
                            </p>
                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-300">
                                #{{ $order->id }} • {{ $order->customer_name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-400">
                                Total Rp{{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada notifikasi order
                    </div>
                @endforelse
            </div>

            <div x-show="tab === 'stock'">
                @forelse ($lowStockProducts as $product)
                    <a
                        href="{{ \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $product->id_product]) }}"
                        class="flex items-start gap-3 border-b border-gray-100 px-4 py-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/40"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 3.75h.008v.008H12v-.008z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                Stok produk menipis
                            </p>
                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-300">
                                {{ $product->name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-400">
                                Sisa stok {{ $product->current_stock }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada notifikasi stok
                    </div>
                @endforelse
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Update saat halaman dimuat</span>
                <span>{{ $totalNotifications }} total</span>
            </div>
        </div>
    </div>
</div>