<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Terapkan middleware 'auth' agar hanya user terotentikasi yang bisa akses.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * ✅ Menambahkan produk ke keranjang (Non-AJAX)
     */
    public function add(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $cart = session()->get('cart', []);
            $quantityToAdd = (int) ($request->quantity ?? 1);

            // Validasi quantity
            if ($quantityToAdd < 1) {
                return redirect()->back()->with('error', 'Quantity tidak valid');
            }

            // Cek stok
            $currentQuantityInCart = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
            if ($product->current_stock < ($currentQuantityInCart + $quantityToAdd)) {
                return redirect()->back()->with('error', 'Stok untuk produk ' . $product->name . ' tidak mencukupi!');
            }

            // Update cart
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantityToAdd;
            } else {
                $cart[$productId] = [
                    "name" => $product->name,
                    "quantity" => $quantityToAdd,
                    "price" => $product->price,
                    "photo_path" => $product->photo_path
                ];
            }

            session()->put('cart', $cart);

            return redirect()->back()->with('success', $product->name . ' berhasil ditambahkan ke keranjang!');
            
        } catch (\Exception $e) {
            Log::error('Cart add error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan produk ke keranjang');
        }
    }

 public function addAjax(Request $request, $productId)
{
    try {
        // ✅ Use DB transaction for consistency
        \DB::beginTransaction();
        
        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);
        $quantityToAdd = (int) ($request->quantity ?? 1);

        // Quick validations
        if ($quantityToAdd < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid quantity'], 400);
        }

        $currentQuantity = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        if ($product->current_stock < ($currentQuantity + $quantityToAdd)) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock'], 400);
        }

        // ✅ Optimize cart update
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantityToAdd;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "quantity" => $quantityToAdd,
                "price" => $product->price,
                "photo_path" => $product->photo_path
            ];
        }

        // ✅ Single session write
        session()->put('cart', $cart);
        \DB::commit();

        // ✅ Quick response with minimal data
        return response()->json([
            'success' => true,
            'message' => $product->name . ' added to cart!',
            'cartCount' => count($cart) // ← CRITICAL for navbar update
        ]);
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Cart add error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'System error'], 500);
    }
}

public function update(Request $request, $productId)
{
    try {
        $quantity = (int) $request->input('quantity');
        
        if ($quantity < 1) {
            return response()->json(['success' => false, 'message' => 'Invalid quantity']);
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
            
            // ✅ Fast total calculation
            $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
            
            return response()->json([
                'success' => true,
                'message' => 'Quantity updated',
                'subtotal' => number_format($cart[$productId]['price'] * $quantity, 0, ',', '.'),
                'total' => number_format($total, 0, ',', '.'),
                'cart_count' => count($cart) // ← CRITICAL
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Item not found']);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'System error']);
    }
}


    /**
     * ✅ Menghapus produk dari keranjang
     */
    public function remove(Request $request, $productId)
    {
        try {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$productId])) {
                $productName = $cart[$productId]['name'];
                unset($cart[$productId]);
                session()->put('cart', $cart);
                
                // Hitung ulang total
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $productName . ' berhasil dihapus dari keranjang',
                    'total' => number_format($total, 0, ',', '.'),
                    'cart_count' => count($cart),
                    'is_empty' => count($cart) === 0
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item'
            ]);
        }
    }

    /**
     * ✅ Clear semua item di keranjang
     */
    public function clear()
    {
        try {
            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengosongkan keranjang'
            ]);
        }
    }

    /**
     * ✅ Get cart count untuk display di navbar
     */
    public function getCartCount()
    {
        try {
            $cart = session()->get('cart', []);
            
            return response()->json([
                'success' => true,
                'cart_count' => count($cart)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get cart count error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'cart_count' => 0
            ]);
        }
    }
}
