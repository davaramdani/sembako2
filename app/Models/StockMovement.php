<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StockMovement extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_stock_movement';
    protected $fillable = [ 'product_id', 'movement_type', 'quantity', 'price_at_time', 'payment_method', 'movement_date', 'note', 'proof_of_payment_path' ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id_product');
    }

    protected static function booted(): void
    {
        // Listener untuk memeriksa stok sebelum membuat pergerakan keluar
        static::creating(function (StockMovement $stockMovement) {
            if ($stockMovement->movement_type === 'out') {
                $product = $stockMovement->product;
                if ($product && $product->current_stock < $stockMovement->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok barang tidak mencukupi. Stok saat ini: ' . $product->current_stock,
                    ]);
                }
            }
        });

        // Listener untuk memperbarui stok produk setelah pergerakan dibuat
        static::created(function (StockMovement $stockMovement) {
            $product = $stockMovement->product;
            if ($product) {
                
                Product::withoutEvents(function () use ($stockMovement, $product) {
                    if ($stockMovement->movement_type === 'in') {
                        $product->increment('current_stock', $stockMovement->quantity);
                    } elseif ($stockMovement->movement_type === 'out') {
                        $product->decrement('current_stock', $stockMovement->quantity);
                    }
                });
            }
        });
    }
}