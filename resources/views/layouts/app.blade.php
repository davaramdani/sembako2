<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16a34a">
    <meta name="description" content="Belanja kebutuhan sehari-hari di Toko Sembako Atok Burhan, Pangkal Pinang. Pilih produk, masukkan ke keranjang, dan pesan dengan mudah.">
    <title>@yield('title', 'Belanja kebutuhan harian') · Atok Burhan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteManifest = is_file($viteManifestPath) ? json_decode(file_get_contents($viteManifestPath), true) : [];
        $viteCss = $viteManifest['resources/css/app.css']['file'] ?? null;
        $viteJs = $viteManifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if($viteCss && $viteJs)
        <link rel="stylesheet" href="{{ asset('build/' . $viteCss) }}">
        <script type="module" src="{{ asset('build/' . $viteJs) }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:p-3">Lewati ke konten utama</a>
    <div class="hidden border-b border-slate-100 bg-slate-50 sm:block">
        <div class="page-shell flex h-8 items-center justify-between text-[11px] text-slate-500">
            <span><i class="fas fa-store mr-1.5 text-green-600" aria-hidden="true"></i> Kebutuhan harian, dekat dengan Anda.</span>
            <div class="flex items-center gap-5">
                <a href="{{ route('shop.index') }}#about" class="hover:text-green-700">Tentang Atok Burhan</a>
                <span><i class="far fa-clock mr-1" aria-hidden="true"></i> 07.00 – 21.00 WIB</span>
            </div>
        </div>
    </div>
    <header id="header" class="sticky top-0 z-40 border-b border-slate-200 bg-white shadow-sm">
        <div class="page-shell">
            <div class="flex flex-wrap items-center gap-3 py-4 lg:gap-6">
                <a href="{{ route('shop.index') }}" aria-label="Atok Burhan — Beranda" class="flex shrink-0 items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-600 text-lg text-white"><i class="fas fa-basket-shopping" aria-hidden="true"></i></span>
                    <span><span class="block text-xl font-extrabold tracking-tight text-green-700">atok burhan<span class="text-amber-500">.</span></span><span class="block text-[9px] font-bold uppercase tracking-[.22em] text-slate-400">Toko Sembako</span></span>
                </a>
                <form role="search" action="{{ route('shop.products') }}" method="GET" class="order-3 w-full lg:order-none lg:flex-1">
                    <label for="site-search" class="sr-only">Cari kebutuhan harian</label>
                    <div class="flex h-11 items-center rounded-lg border border-slate-300 bg-white transition focus-within:border-green-600 focus-within:ring-2 focus-within:ring-green-100">
                        <i class="fas fa-search ml-4 text-sm text-slate-400" aria-hidden="true"></i>
                        <input id="site-search" type="search" name="search" value="{{ request('search', $search ?? '') }}" placeholder="Cari beras, minyak, gula, dan kebutuhan lainnya..." class="min-w-0 flex-1 border-0 bg-transparent px-3 text-sm outline-none focus-visible:ring-0">
                        <button type="submit" aria-label="Cari produk" class="m-1 flex h-8 w-10 items-center justify-center rounded-md bg-green-600 text-sm text-white hover:bg-green-700"><i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </form>
                <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3 lg:ml-0">
                    <a href="{{ route('cart.index') }}" aria-label="Keranjang belanja" class="relative mr-1 flex h-10 w-10 items-center justify-center rounded-lg text-lg text-slate-600 hover:bg-green-50 hover:text-green-700">
                        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                        <span class="cart-count cart-badge" data-cart-count style="{{ count(session('cart', [])) ? '' : 'display:none' }}">{{ count(session('cart', [])) }}</span>
                    </a>
                    <span class="hidden h-7 w-px bg-slate-200 sm:block" aria-hidden="true"></span>
                    @guest
                        <a href="{{ route('login') }}" class="btn-secondary !min-h-[36px] !border-green-600 !px-3 !py-2 !text-xs !text-green-700 sm:!px-5">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary hidden !min-h-[36px] !px-5 !py-2 !text-xs sm:inline-flex">Daftar</a>
                    @endguest
                    @auth
                        <details class="relative">
                            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg p-1.5 hover:bg-slate-50">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-sm font-bold text-green-700">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                                <span class="hidden max-w-[110px] truncate text-xs font-bold sm:block">{{ Auth::user()->name }}</span><i class="fas fa-chevron-down text-[10px]" aria-hidden="true"></i>
                            </summary>
                            <div class="absolute right-0 mt-3 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                                <p class="truncate border-b border-slate-100 px-3 py-2 text-xs text-slate-500">{{ Auth::user()->name }}</p>
                                <a href="{{ route('user.orders') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-green-50">Pesanan saya</a>
                                <a href="{{ route('user.history') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-green-50">Riwayat pesanan</a>
                                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button></form>
                            </div>
                        </details>
                    @endauth
                </div>
            </div>
            <div class="flex items-center justify-between gap-4 border-t border-slate-100">
                <nav aria-label="Navigasi utama" class="flex min-w-0 items-center gap-6 overflow-x-auto">
                    <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}" @if(request()->routeIs('shop.index')) aria-current="page" @endif>Beranda</a>
                    <a href="{{ route('shop.products') }}" class="nav-link {{ request()->routeIs('shop.products') ? 'active' : '' }}" @if(request()->routeIs('shop.products')) aria-current="page" @endif>Semua produk</a>
                    <a href="{{ route('shop.index') }}#categories" class="nav-link">Kategori</a>
                    @auth<a href="{{ route('user.orders') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">Pesanan saya</a>@endauth
                    @guest<a href="{{ route('register') }}" class="nav-link sm:hidden">Daftar</a>@endguest
                </nav>
                <span class="hidden shrink-0 text-[11px] text-slate-500 md:block"><i class="fas fa-map-marker-alt mr-1 text-green-600" aria-hidden="true"></i> Toko di <strong class="font-semibold text-slate-700">Pangkal Pinang</strong></span>
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1">
        @if(session('success') || session('error'))
            <div class="page-shell pt-5" role="{{ session('error') ? 'alert' : 'status' }}">
                <div class="rounded-xl border px-4 py-3 text-sm {{ session('error') ? 'border-red-200 bg-red-50 text-red-700' : 'border-green-200 bg-green-50 text-green-800' }}">{{ session('error') ?: session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-8 border-t border-slate-200 bg-white">
        <div class="page-shell">
            <div class="grid gap-8 py-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <a href="{{ route('shop.index') }}" class="text-xl font-extrabold tracking-tight text-green-700">atok burhan<span class="text-amber-500">.</span></a>
                    <p class="mt-3 max-w-sm text-sm leading-7 text-slate-500">Teman belanja kebutuhan sehari-hari. Dari isi dapur hingga keperluan rumah, temukan pilihan Anda di Toko Sembako Atok Burhan.</p>
                </div>
                <div>
                    <h2 class="mb-4 text-sm font-bold">Jelajahi toko</h2>
                    <div class="flex flex-col items-start gap-3 text-xs text-slate-500">
                        <a href="{{ route('shop.products') }}" class="hover:text-green-700">Semua produk</a>
                        <a href="{{ route('cart.index') }}" class="hover:text-green-700">Keranjang belanja</a>
                        <a href="{{ route('user.orders') }}" class="hover:text-green-700">Pesanan saya</a>
                        <a href="{{ route('shop.index') }}#about" class="hover:text-green-700">Tentang kami</a>
                    </div>
                </div>
                <div>
                    <h2 class="mb-4 text-sm font-bold">Kunjungi kami</h2>
                    <p class="text-xs leading-7 text-slate-500"><i class="fas fa-map-marker-alt mr-2 text-green-600" aria-hidden="true"></i> Pangkal Pinang, Indonesia<br><i class="far fa-clock mr-2 text-green-600" aria-hidden="true"></i> Senin – Minggu<br><span class="ml-6">07.00 – 21.00 WIB</span></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 py-5 text-[11px] text-slate-400">
                <p>&copy; {{ date('Y') }} Toko Sembako Atok Burhan.</p><span>Belanja dekat, kebutuhan lengkap.</span>
            </div>
        </div>
    </footer>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/cart-final.js') }}" defer></script>
    @stack('scripts')
    {{-- Existing customer support integration. --}}
    <script>
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var support = document.createElement('script'), first = document.getElementsByTagName('script')[0];
            support.async = true;
            support.src = 'https://embed.tawk.to/69d1ec269680621c33789bbb/1jle04ucm';
            support.charset = 'UTF-8';
            support.setAttribute('crossorigin', '*');
            first.parentNode.insertBefore(support, first);
        })();
    </script>
</body>
</html>
