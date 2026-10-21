@extends('layouts.app')

@section('title', 'Keranjang belanja')

@section('content')
<div class="bg-slate-50 py-6 sm:py-8">
    <div class="page-shell" id="cart-container-body">
        @include('cart.partials.cart_body')
    </div>
</div>
@endsection
