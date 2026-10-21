@extends('layouts.app')

@section('content')
<div class="page-shell py-8 sm:py-12">
    <div class="mx-auto grid max-w-4xl items-start gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="hidden pt-16 lg:block">
            <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700"><i class="fas fa-store" aria-hidden="true"></i> Toko Sembako Atok Burhan</span>
            <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-gray-900">Mulai belanja kebutuhan<br><span class="text-green-600">rumah Anda.</span></h1>
            <p class="mt-4 text-sm leading-7 text-gray-500">Buat akun untuk belanja sembako, menyimpan informasi pengiriman, dan melihat riwayat pesanan dengan mudah.</p>
            <div class="mt-8 rounded-2xl border border-green-100 bg-green-50 p-6">
                <i class="fas fa-basket-shopping text-3xl text-green-600" aria-hidden="true"></i>
                <p class="mt-4 font-semibold text-gray-900">Dari toko kami, untuk keluarga Anda.</p>
                <p class="mt-2 text-sm leading-6 text-gray-600">Temukan kebutuhan dapur dan sehari-hari dalam satu tempat.</p>
            </div>
        </div>
        <section class="surface mx-auto w-full max-w-md p-6 sm:p-8" aria-labelledby="register-title">
            <div class="mb-2 flex items-center justify-between gap-4">
                <h2 id="register-title" class="text-2xl font-bold tracking-tight text-gray-900">Daftar akun</h2>
                <a href="{{ route('login') }}" class="text-sm font-semibold text-green-600 hover:text-green-700">Masuk</a>
            </div>
            <p class="mb-6 text-sm leading-6 text-gray-500">Lengkapi data untuk mulai berbelanja.</p>
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" placeholder="Nama lengkap Anda" class="field-input" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name') <p id="name-error" class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" class="field-input" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email') <p id="email-error" class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="address" class="mb-2 block text-sm font-semibold text-gray-700">Alamat lengkap</label>
                    <textarea name="address" id="address" rows="3" autocomplete="street-address" placeholder="Nama jalan, nomor rumah, dan kota" class="field-input resize-y" required @error('address') aria-invalid="true" aria-describedby="address-error" @enderror>{{ old('address') }}</textarea>
                    @error('address') <p id="address-error" class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" id="password" autocomplete="new-password" placeholder="Buat password Anda" class="field-input" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    @error('password') <p id="password-error" class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" placeholder="Ulangi password Anda" class="field-input" required>
                </div>
                <button type="submit" class="btn-primary w-full">Daftar</button>
            </form>
            <p class="mt-6 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-700">Masuk di sini</a></p>
        </section>
    </div>
</div>
@endsection