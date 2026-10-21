<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_product';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [ 'name', 'photo_path', 'price', 'current_stock' ];

    public function stockMovements() { return $this->hasMany(StockMovement::class, 'product_id', 'id_product'); }

    protected static function booted(): void
    {
        static::created(function (Product $product) {
            if (!empty($product->current_stock) && $product->current_stock > 0) {
                // Buat StockMovement TANPA memicu event-nya sendiri
                StockMovement::withoutEvents(function () use ($product) {
                    StockMovement::create([
                        'product_id' => $product->id_product, 'movement_type' => 'in',
                        'quantity' => $product->current_stock, 'price_at_time' => $product->price,
                        'movement_date' => now('Asia/Jakarta'), 'note' => 'Stok awal untuk produk baru.',
                    ]);
                });
            }
        });

        static::updated(function (Product $product) {
            if ($product->wasChanged('current_stock')) {
                $originalStock = $product->getOriginal('current_stock') ?? 0;
                $newStock = $product->current_stock ?? 0;
                $difference = $newStock - $originalStock;
                
                // Buat StockMovement TANPA memicu event-nya sendiri
                StockMovement::withoutEvents(function () use ($product, $difference) {
                    if ($difference > 0) {
                        StockMovement::create([
                            'product_id' => $product->id_product, 'movement_type' => 'in', 'quantity' => $difference,
                            'price_at_time' => $product->price, 'movement_date' => now('Asia/Jakarta'), 'note' => 'Stok diperbarui manual dari halaman produk.',
                        ]);
                    } elseif ($difference < 0) {
                        StockMovement::create([
                            'product_id' => $product->id_product, 'movement_type' => 'out', 'quantity' => abs($difference),
                            'price_at_time' => $product->price, 'movement_date' => now('Asia/Jakarta'), 'note' => 'Stok diperbarui manual dari halaman produk.',
                        ]);
                    }
                });
            }
        });
    }
}