<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ✅ PENTING: Set primary key yang benar
     * Berdasarkan CheckoutController Anda menggunakan id_user
     */
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ RELATIONSHIP: User has many orders
     * Specify foreign key dan local key yang tepat
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id_user');
        //                                   ^foreign   ^local
    }
}
