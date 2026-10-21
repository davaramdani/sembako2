@extends('layouts.app')

@section('content')
<div class="page-shell py-8 sm:py-14">
    <div class="mx-auto grid max-w-4xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="hidden lg:block">
            <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700"><i class="fas fa-store" aria-hidden="true"></i> Toko Sembako Atok Burhan</span>
            <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-gray-900">Kebutuhan sehari-hari,<br><span class="text-green-600">lebih dekat dari sini.</span></h1>
            <p class="mt-4 text-sm leading-7 text-gray-500">Masuk untuk melanjutkan belanja dan melihat perkembangan pesanan Anda.</p>
            <div class="mt-8 space-y-4">
                <div class="flex items-center gap-3 text-sm text-gray-600"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600"><i class="fas fa-basket-shopping" aria-hidden="true"></i></span> Belanja sembako dalam satu tempat</div>
                <div class="flex items-center gap-3 text-sm text-gray-600"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600"><i class="fas fa-box" aria-hidden="true"></i></span> Pantau pesanan dengan mudah</div>
            </div>
        </div>
        <section class="surface mx-auto w-full max-w-md p-6 sm:p-8" aria-labelledby="login-title">
            <div class="mb-7 flex items-center justify-between gap-4">
                <h2 id="login-title" class="text-2xl font-bold tracking-tight text-gray-900">Masuk</h2>
                <a href="{{ route('register') }}" class="text-sm font-semibold text-green-600 hover:text-green-700">Daftar</a>
            </div>
            <p class="mb-6 text-sm leading-6 text-gray-500">Selamat datang kembali di Atok Burhan.</p>
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" class="field-input" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email') <p id="email-error" class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" id="password" autocomplete="current-password" placeholder="Masukkan password" class="field-input" required>
                </div>
                <button type="submit" class="btn-primary w-full">Masuk</button>
            </form>
            <p class="mt-6 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-700">Daftar sekarang</a></p>
        </section>
    </div>
</div>
@endsection