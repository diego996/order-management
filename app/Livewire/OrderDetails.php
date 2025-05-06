<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Models\Order;

class OrderDetails extends Component
{
    public Order $order;

    public function mount(OrderService $service, int $order)
    {
        // carica l'ordine con cliente e prodotti collegati
        $this->order = $service
            ->get($order)
            ->load('customer', 'products');
    }

    public function render()
    {
        return view('livewire.order-details')->layout('layout.app');
    }
}
