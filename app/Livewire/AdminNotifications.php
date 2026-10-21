<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;

class AdminNotifications extends Component
{
    public function getLowStockProductsProperty()
    {
        return Product::where('current_stock', '<', 10)
            ->orderBy('current_stock', 'asc')
            ->get();
    }

    public function getPendingOrdersProperty()
    {
        return Order::where('status', 'Menunggu Pembayaran')
            ->latest()
            ->get();
    }

    public function getTotalNotificationsProperty()
    {
        return $this->lowStockProducts->count() + $this->pendingOrders->count();
    }

    public function render()
    {
        return view('livewire.admin-notifications');
    }
}