@extends('layouts.app')
@section('title', 'Belanja kebutuhan harian')
@section('content')
<div class="page-shell py-5 sm:py-7">
    <section id="home" aria-labelledby="hero-title" class="relative isolate overflow-hidden rounded-2xl bg-[#eaf3df]">
        <div class="absolute -left-12 -top-24 h-64 w-64 rounded-full border-[40px] border-white/25" aria-hidden="true"></div>
        <div class="grid items-center md:grid-cols-2">
            <div class="relative z-10 px-6 pb-3 pt-8 sm:px-10 sm:py-10 lg:pl-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/75 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span> Dari toko kami, untuk rumah Anda</span>
                <h1 id="hero-title" class="mt-5 text-3xl font-extrabold leading-[1.2] tracking-tight text-brand-900 sm:text-4xl lg:text-[44px]">Isi dapur,<br>tenang setiap hari.</h1>
                <p class="mt-4 max-w-sm text-sm leading-6 text-[#597053]">Belanja kebutuhan harian jadi lebih mudah. Pilih yang Anda butuhkan, kami siapkan dengan sepenuh hati.</p>
                <a href="{{ route('shop.products') }}" class="btn-primary mt-6 !bg-brand-700 hover:!bg-brand-900">Mulai belanja <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
            </div>
            <div class="relative flex items-center justify-center px-4 pb-3 md:py-5">
                <img src="{{ asset('images/grocery-bag.svg') }}" alt="Ilustrasi tas belanja Atok Burhan berisi kebutuhan dapur" width="600" height="430" fetchpriority="high" class="max-h-56 w-full object-contain sm:max-h-72 md:max-h-[340px]">
            </div>
        </div>
    </section>

    <div class="mt-4 grid grid-cols-1 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white px-5 py-1 sm:grid-cols-3 sm:divide-x sm:divide-y-0 sm:py-4">
        <div class="flex items-center gap-3 py-3 sm:py-0"><i class="fas fa-box-open text-xl text-green-600" aria-hidden="true"></i><div><p class="text-xs font-bold text-slate-800">Kebutuhan sehari-hari</p><p class="mt-1 text-[11px] text-slate-500">Pilihan produk dalam satu tempat</p></div></div>
        <div class="flex items-center gap-3 py-3 sm:py-0 sm:pl-5"><i class="fas fa-receipt text-xl text-green-600" aria-hidden="true"></i><div><p class="text-xs font-bold text-slate-800">Belanja lebih praktis</p><p class="mt-1 text-[11px] text-slate-500">Pesan dan pantau melalui akun Anda</p></div></div>
        <div class="flex items-center gap-3 py-3 sm:py-0 sm:pl-5"><i class="fas fa-store text-xl text-green-600" aria-hidden="true"></i><div><p class="text-xs font-bold text-slate-800">Toko lokal Anda</p><p class="mt-1 text-[11px] text-slate-500">Melayani dari Pangkal Pinang</p></div></div>
    </div>

    <section id="categories" aria-labelledby="category-title" class="mt-8">
        <div class="mb-4 flex items-center justify-between gap-3"><h2 id="category-title" class="section-title">Mau belanja apa hari ini?</h2><a href="{{ route('shop.products') }}" class="hidden text-xs font-bold text-green-700 hover:text-green-900 sm:inline">Jelajahi produk <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a></div>
        <div class="-mx-4 flex gap-3 overflow-x-auto px-4 pb-2 sm:mx-0 sm:px-0">
            @foreach([
                ['Semua produk', '', 'fa-border-all', 'bg-green-50 text-green-600'],
                ['Beras', 'beras', 'fa-wheat-awn', 'bg-amber-50 text-amber-600'],
                ['Minyak goreng', 'minyak', 'fa-bottle-droplet', 'bg-yellow-50 text-yellow-600'],
                ['Gula', 'gula', 'fa-cubes-stacked', 'bg-rose-50 text-rose-500'],
                ['Tepung', 'tepung', 'fa-cookie-bite', 'bg-orange-50 text-orange-500'],
                ['Mi instan', 'mie', 'fa-bowl-food', 'bg-red-50 text-red-500'],
                ['Susu', 'susu', 'fa-glass-water', 'bg-sky-50 text-sky-500']
            ] as [$label, $query, $icon, $color])
                <a href="{{ route('shop.products', $query ? ['search' => $query] : []) }}" class="category-link">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl text-2xl {{ $color }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                    <span class="whitespace-nowrap">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section id="products" aria-labelledby="products-title" class="mt-8">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div><h2 id="products-title" class="section-title">Pilihan untuk kebutuhanmu</h2><p class="mt-1.5 text-xs text-slate-500 sm:text-sm">Lengkapi persediaan rumah, mulai dari sini.</p></div>
            <a href="{{ route('shop.products') }}" class="shrink-0 text-xs font-bold text-green-700 hover:text-green-900">Lihat semua <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-6">
            @forelse($products->take(6) as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="surface col-span-full px-6 py-12 text-center"><i class="fas fa-box-open mb-3 text-3xl text-green-600" aria-hidden="true"></i><h3 class="font-bold">Produk sedang disiapkan</h3><p class="mt-2 text-sm text-slate-500">Kunjungi kembali untuk melihat stok terbaru dari toko kami.</p></div>
            @endforelse
        </div>
    </section>

    <section class="mt-8 flex flex-col items-start justify-between gap-5 rounded-2xl border border-[#eee4cc] bg-[#faf5e9] px-6 py-6 sm:flex-row sm:items-center sm:px-8">
        <div class="flex items-center gap-4"><span class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-xl text-amber-700 sm:flex"><i class="fas fa-basket-shopping" aria-hidden="true"></i></span><div><h2 class="text-lg font-extrabold text-[#695334]">Sudah cek persediaan dapur?</h2><p class="mt-1 text-xs leading-6 text-[#8a7859]">Cari dan pilih kebutuhan Anda sebelum kehabisan di rumah.</p></div></div>
        <a href="{{ route('shop.products') }}" class="inline-flex shrink-0 items-center gap-3 text-sm font-bold text-[#695334] hover:text-green-700">Lengkapi sekarang <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
    </section>

    <section id="about" class="mb-3 mt-10 grid gap-5 border-t border-slate-200 pt-8 sm:grid-cols-2">
        <div><span class="text-[10px] font-bold uppercase tracking-[.18em] text-green-700">Kenali toko kami</span><h2 class="mt-2 text-xl font-extrabold tracking-tight">Tetangga untuk kebutuhan harian Anda.</h2></div>
        <p class="text-sm leading-7 text-slate-500">Toko Sembako Atok Burhan melayani kebutuhan sehari-hari Anda di Pangkal Pinang. Kini, memilih produk dan memantau pesanan dapat dilakukan dengan mudah melalui toko online kami.</p>
    </section>
</div>
@endsection
