@extends('layouts.app')
@section('title', $search ? 'Hasil pencarian: ' . $search : 'Semua produk')
@section('content')
<div class="page-shell py-6 sm:py-8">
    <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-slate-500"><a href="{{ route('shop.index') }}" class="hover:text-green-700">Beranda</a><i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i><span class="text-slate-800">Semua produk</span></nav>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div><h1 class="section-title">{{ $search ? 'Hasil pencarian' : 'Semua kebutuhan, satu tempat.' }}</h1><p class="mt-2 text-sm text-slate-500">@if($search)Menampilkan {{ $products->total() }} produk untuk <strong class="font-semibold text-slate-800">“{{ $search }}”</strong>@else{{ $products->total() }} produk tersedia untuk melengkapi kebutuhan Anda.@endif</p></div>
        @if($search)<a href="{{ route('shop.products') }}" class="btn-secondary !text-xs"><i class="fas fa-times" aria-hidden="true"></i> Hapus pencarian</a>@endif
    </div>
    <div class="mb-7 flex gap-2 overflow-x-auto pb-2" aria-label="Pencarian cepat kebutuhan">
        @foreach(['' => 'Semua produk', 'beras' => 'Beras', 'minyak' => 'Minyak goreng', 'gula' => 'Gula', 'tepung' => 'Tepung', 'mie' => 'Mi instan', 'susu' => 'Susu'] as $query => $label)
            <a href="{{ route('shop.products', $query ? ['search' => $query] : []) }}" @if(strtolower($search ?? '') === $query) aria-current="true" @endif class="shrink-0 rounded-full border px-4 py-2.5 text-xs font-semibold {{ strtolower($search ?? '') === $query ? 'border-green-600 bg-green-50 text-green-700' : 'border-slate-200 bg-white text-slate-600 hover:border-green-500 hover:text-green-700' }}">{{ $label }}</a>
        @endforeach
    </div>
    @if($products->count())
        <div class="mb-4 flex items-center justify-between text-xs text-slate-500"><span>{{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk</span><span>Urutan: <strong class="font-semibold text-slate-700">Nama A–Z</strong></span></div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-6">
            @foreach($products as $product)<x-product-card :product="$product" />@endforeach
        </div>
        <div class="mt-8">{{ $products->appends(['search' => $search])->links() }}</div>
    @else
        <div class="surface px-5 py-16 text-center">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-2xl text-green-600"><i class="fas fa-search" aria-hidden="true"></i></span>
            <h2 class="mt-5 text-xl font-bold">{{ $search ? 'Produknya belum ketemu' : 'Produk sedang disiapkan' }}</h2>
            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">{{ $search ? 'Coba kata kunci yang lebih singkat atau jelajahi produk lain yang tersedia di toko kami.' : 'Belum ada produk tersedia. Silakan kunjungi toko kami kembali untuk melihat stok terbaru.' }}</p>
            <a href="{{ route('shop.products') }}" class="btn-primary mt-6">Lihat semua produk</a>
        </div>
    @endif
</div>
@endsection
