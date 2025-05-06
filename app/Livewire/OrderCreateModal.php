<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Services\CustomerService;
use App\Services\ProductService;
use Illuminate\Validation\Rule;

class OrderCreateModal extends Component
{
    public bool $show = false;

    public int $customer_id;
    public string $order_date;
    public string $order_code;
    public string $status = 'pending';
    public array $products = [];

    public array $availableProducts = [];
    public array $availableCustomers = [];

    protected $listeners = [
        'showCreateModal' => 'open',
    ];

    public function rules(): array
    {
        return [
            'customer_id'          => ['required', 'exists:customers,id'],
            'order_date'           => ['required','date'],
            'order_code'           => ['required','unique:orders,order_code'],
            'status'               => ['required', Rule::in(['pending','processing','completed','cancelled'])],
            'products'             => ['required','array','min:1'],
            'products.*.product_id'=> ['required','exists:products,id'],
            'products.*.quantity'  => ['required','integer','min:1'],
            'products.*.price'     => ['required','numeric','min:0'],
        ];
    }

    public function mount(CustomerService $customers, ProductService $products)
    {
        $this->availableCustomers = $customers->all()->toArray();
        $this->availableProducts  = $products->all()->toArray();
        $this->order_date = now()->format('Y-m-d');
    }

    public function open()
    {
        $this->resetValidation();
        $this->reset(['customer_id','order_code','status','products']);
        $this->show = true;
    }

    public function addProductLine()
    {
        $this->products[] = ['product_id'=>null,'quantity'=>1,'price'=>0];
    }

    public function removeProductLine($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);
    }

    public function save(OrderService $orders)
    {
        $data = $this->validate();

        // calcolo totale
        $data['total'] = collect($data['products'])->sum(fn($p)=> $p['quantity'] * $p['price']);

        $orders->create($data);

        $this->emitUp('orderCreated');
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.order-create-modal');
    }
}
