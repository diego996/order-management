<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use App\Services\OrderService;
use App\Services\CustomerService;
use App\Services\ProductService;
use Illuminate\Validation\Rule;

class OrderCreateModal extends Component
{
    public bool   $show             = false;
    public int    $customer_id;
    public string $order_date;
    public string $order_code;
    public string $status           = 'pending';
    public array  $products         = [];
    public array  $availableCustomers = [];
    public array  $availableProducts  = [];

    protected function rules(): array
    {
        return [
            'customer_id'           => ['required','exists:customers,id'],
            'order_date'            => ['required','date'],
            'order_code'            => ['required','unique:orders,order_code'],
            'status'                => ['required', Rule::in(['pending','processing','completed','cancelled'])],
            'products'              => ['required','array','min:1'],
            'products.*.product_id' => ['required','exists:products,id'],
            'products.*.quantity'   => ['required','integer','min:1'],
            // non servono regole su price, è calcolato automaticamente
        ];
    }

    public function mount(CustomerService $cs, ProductService $ps)
    {
        $this->availableCustomers = $cs->all()->toArray();
        $this->availableProducts  = $ps->all()->toArray();
        $this->order_date = now()->format('Y-m-d');
    }

    public function open()
    {
        $this->resetValidation();
        $this->reset(['customer_id','order_date','order_code','status','products']);
        $this->order_date = now()->format('Y-m-d');
        $this->show = true;
    }

    public function cancel()
    {
     
        $this->resetValidation();
        $this->reset(['customer_id','order_date','order_code','status','products']);
        $this->show = false;
    }

    public function addProductLine()
    {
        $this->products[] = [
            'product_id' => null,
            'quantity'   => 0,
            'price'      => 0,  // subtotal: verrà ricalcolato
        ];
    }

    public function removeProductLine(int $idx)
    {
        unset($this->products[$idx]);
        $this->products = array_values($this->products);
    }

    public function updated($propertyName)
    {
        // ogni volta che cambio prodotto o quantità ricalcolo il subtotal
        if (Str::startsWith($propertyName, 'products.')) {
            [$_, $idx, $field] = explode('.', $propertyName);
            $idx = (int) $idx;
            $line = $this->products[$idx];

            // se ho selezionato un prodotto e una quantità
            if (!empty($line['product_id']) && !empty($line['quantity'])) {
                // prendo il prezzo unitario dal catalogo dei prodotti
                $prod = collect($this->availableProducts)
                    ->firstWhere('id', $line['product_id']);
                $unitPrice = $prod['price'] ?? 0;
                // ricalcolo il subtotal
                $this->products[$idx]['price'] = $unitPrice * $line['quantity'];
            } else {
                $this->products[$idx]['price'] = 0;
            }
        }
       
    }

    public function save(OrderService $orders)
    {
        $this->validate();

        // preparo i dati per OrderService
        $data = [
            'customer_id' => $this->customer_id,
            'order_date'  => $this->order_date,
            'order_code'  => $this->order_code,
            'status'      => $this->status,
            'products'    => array_map(fn($line) => [
                'product_id' => $line['product_id'],
                'quantity'   => $line['quantity'],
                'price'      => $line['price'],
            ], $this->products),
        ];

        // totale = somma dei subtotali
        $data['total'] = array_sum(array_column($data['products'], 'price'));
        $order_id = $orders->create($data)->id;
        $this->dispatch('orderCreated');
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.order-create-modal');
    }
}
