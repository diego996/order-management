<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\OrderService;
use App\Models\Customer;

class OrderList extends Component
{
    use WithPagination;

    public int    $perPage        = 15;
    public ?int   $filterCustomer = null;
    public string $filterStatus   = '';
    public string $filterDateFrom = '';
    public string $filterDateTo   = '';
    public bool   $showCreateModal= false;
    public string $sortField       = 'order_date';
    public string $sortDirection   = 'desc';
    protected $listeners = [
        'orderCreated' => 'handleOrderCreated',
    ];

    // Mantieni i filtri nella query string (opzionale)
    protected $queryString = [
        'filterCustomer' => ['except' => null],
        'filterStatus'   => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo'   => ['except' => ''],
        'perPage'        => ['except' => 15],
        'sortField'      => ['except' => 'order_date'],
        'sortDirection'  => ['except' => 'desc'],
    ];
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            // toggle direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // nuovo campo, resetto direction ad asc
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    public function handleOrderCreated()
    {
        // Dopo aver creato un ordine, torniamo a pagina 1
        $this->resetPage();
    }

    // Per ogni filtro, quando cambia, resetta la paginazione
    public function updatedFilterCustomer() { $this->resetPage(); }
    public function updatedFilterStatus()   { $this->resetPage(); }
    public function updatedFilterDateFrom(){ $this->resetPage(); }
    public function updatedFilterDateTo()  { $this->resetPage(); }

    // Reset manuale di tutti i filtri
    public function resetFilters()
    {
        $this->reset(['filterCustomer','filterStatus','filterDateFrom','filterDateTo']);
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function render(OrderService $orders)
    {
        $filters = [
            'customer_id' => $this->filterCustomer,
            'status'      => $this->filterStatus,
            'date_from'   => $this->filterDateFrom,
            'date_to'     => $this->filterDateTo,

        ];

        $ordersPaginated = $orders->list(
            filters:       $filters,
            perPage:       $this->perPage,
            sortField:     $this->sortField,
            sortDirection: $this->sortDirection,
        );

        return view('livewire.order-list', [
            'orders'    => $ordersPaginated,
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
