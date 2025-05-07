<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Models\Order;
use App\Services\ProductService;
use Illuminate\Support\Str;

class OrderDetails extends Component
{
    public Order $order;
    public bool   $editing     = false;
    public string $order_date;
    public string $order_code;
    public string $status;

    public array $availableStatuses = [
        'pending', 'processing', 'completed', 'cancelled'
    ];
    public array  $lines               = [];
    public array  $availableProducts   = [];


    protected function rules(): array
    {
        return [
            'order_date'         => ['required','date'],
            'order_code'         => ['required','unique:orders,order_code,'.$this->order->id],
            'status'             => ['required','in:'.implode(',', $this->availableStatuses)],
            'lines'              => ['required','array','min:1'],
            'lines.*.product_id' => ['required','exists:products,id'],
            'lines.*.quantity'   => ['required','integer','min:1'],
            'lines.*.price'      => ['required','numeric','min:0'],
        ];
    }

    public function mount(
        Order $order,
        OrderService $orderService,
        ProductService $productService
    )
    {
        // Carico le relazioni con il service (o direttamente, se preferisci)
        $this->order = $orderService
            ->get($order->id)
            ->load('customer', 'products');

        $this->order_date = $this->order->order_date->format('Y-m-d');
        $this->order_code = $this->order->order_code;
        $this->status     = $this->order->status;
        $this->lines = $this->order
        ->products
        ->map(fn($p) => [
            'product_id' => $p->id,
            'quantity'   => $p->pivot->quantity,
            'price'      => $p->pivot->price,
        ])
        ->toArray();
        $this->availableProducts = $productService
        ->all()
        ->map(fn($p) => [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => $p->price,
        ])
        ->toArray();
    }

    public function edit()
    {
        $this->editing = true;
    }

    public function cancel()
    {
        // ripristina i valori originali
        $this->order_date = $this->order->order_date->toDateString();
        $this->order_code = $this->order->order_code;
        $this->status     = $this->order->status;

        $this->lines = $this->order
            ->products
            ->map(fn($p) => [
                'product_id' => $p->id,
                'quantity'   => $p->pivot->quantity,
                'price'      => $p->pivot->price,
            ])
            ->toArray();

        $this->editing = false;
        $this->resetValidation();
    }

    public function addLine()
    {
        $this->lines[] = [
            'product_id' => null,
            'quantity'   => 1,
            'price'      => 0,
        ];
    }

    public function removeLine(int $idx)
    {
        unset($this->lines[$idx]);
        $this->lines = array_values($this->lines);
    }

    public function updated($property)
    {
        // se cambio prodotto o quantità, ricalcolo price
        if (Str::startsWith($property, 'lines.')) {
            [$prefix, $idx, $field] = explode('.', $property);
            $idx = (int)$idx;
            $line = $this->lines[$idx] ?? null;

            if ($line && ! is_null($line['product_id'])) {
                // trovo il prezzo unitario
                $prod = collect($this->availableProducts)
                         ->firstWhere('id', $line['product_id']);
                $unit = $prod['price'] ?? 0;
                $qty  = $line['quantity'] ?? 1;

                $this->lines[$idx]['price'] = $unit * $qty;
            }
        }
    }

    public function save(OrderService $service)
    {
        $this->validate();

        $data = [
            'order_date' => $this->order_date,
            'order_code' => $this->order_code,
            'status'     => $this->status,
            'products'   => $this->lines,
        ];

        // chiama il service per aggiornare
        $updated = $service->update($this->order->id, $data);

        // aggiorna istanza locale e chiudi edit
        $this->order    = $updated->load('customer','products');
        $this->editing  = false;
    }
    
    public function render()
    {
        return view('livewire.order-details');
    }
}
