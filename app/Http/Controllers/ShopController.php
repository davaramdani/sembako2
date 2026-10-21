<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::where('current_stock', '>', 0)->get();
        return view('shop.index', compact('products'));
    }

    // ✅ Method baru untuk halaman khusus semua produk + search
    public function products(Request $request)
    {
        $search = $request->input('search');

        $products = Product::where('current_stock', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(12);

        return view('shop.products', compact('products', 'search'));
    }
}