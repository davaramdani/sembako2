<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'customer_name',
        'customer_email',
        'shipping_address',
        'payment_method',
        'payment_proof',
        'payment_status',
        'payment_expires_at',
        'snap_token',
        'tracking_message',
    ];

    protected $casts = [
        'payment_expires_at' => 'datetime',
        'total_price' => 'decimal:0',
    ];

    /**
     * ✅ SCOPE: Pesanan Aktif (hanya yang belum expired)
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'Dibatalkan')
                    ->where('status', '!=', 'Expired')
                    ->where(function($q) {
                        $q->whereNull('payment_expires_at')
                          ->orWhere('payment_expires_at', '>', now());
                    });
    }

    /**
     * ✅ METHOD: Cek apakah pembayaran sudah expired
     */
    public function isPaymentExpired()
    {
        if (!$this->payment_expires_at) {
            return false;
        }
        return $this->payment_expires_at->isPast();
    }

    /**
     * ✅ RELATIONSHIP: Order belongs to User
     * Specify foreign key dan owner key yang benar
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
        //                                   ^foreign   ^owner
    }

    /**
     * ✅ RELATIONSHIP: Order has many items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Status display accessor (sama seperti sebelumnya)
    public function getStatusDisplayAttribute()
    {
        $statusMap = [
            'Menunggu Pembayaran' => [
                'text' => 'Menunggu Pembayaran',
                'color' => 'warning',
                'icon' => 'fa-clock'
            ],
            'Dibayar' => [
                'text' => 'Dibayar',
                'color' => 'info', 
                'icon' => 'fa-credit-card'
            ],
            'Dikemas' => [
                'text' => 'Sedang Dikemas',
                'color' => 'primary',
                'icon' => 'fa-box'
            ],
            'Dikirim' => [
                'text' => 'Dalam Pengiriman',
                'color' => 'info',
                'icon' => 'fa-truck'
            ],
            'Selesai' => [
                'text' => 'Selesai',
                'color' => 'success',
                'icon' => 'fa-check-circle'
            ],
            'Dibatalkan' => [
                'text' => 'Dibatalkan',
                'color' => 'danger',
                'icon' => 'fa-times-circle'
            ]
        ];

        return $statusMap[$this->status] ?? [
            'text' => $this->status,
            'color' => 'secondary',
            'icon' => 'fa-question-circle'
        ];
    }
}
