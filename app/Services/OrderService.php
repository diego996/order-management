<?php


namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Events\OrderDeleted;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class OrderService
{
    public function __construct(protected OrderRepositoryInterface $repo) {}

    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->repo->list($filters, $perPage, $sortField, $sortDirection);
    }

    public function all(array $filters = []): \Illuminate\Support\Collection
    {
        return $this->repo->all($filters);
    }

    public function get(int $id): ?Order
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Order
    {
        $lines = $data['products'] ?? [];
        unset($data['products']);
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        } else {
            $data['user_id'] = 1; // o un valore predefinito
        }

        $order = $this->repo->create($data);
        $total = 0;

        foreach ($lines as $line) {
            $order->products()->attach($line['product_id'], [
            'quantity'   => $line['quantity'],
            'price'      => $line['price'],
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }
   
        event(new OrderCreated($order));
        return $order;
    }

    public function update(int $id, array $data): Order
    {
        $hasLines = array_key_exists('products', $data);

        if ($hasLines) {
            // estraggo e tolgo products dal payload
            $lines = $data['products'];
            unset($data['products']);
        }
    
        // recupero e aggiorno i campi dell’ordine
        $order = $this->repo->find($id)
               ?? throw new RuntimeException("Order #{$id} non trovato");
        $order = $this->repo->update($order, $data);
    
        if ($hasLines) {
            // sincronizzo pivot
            $syncData = collect($lines)
                ->mapWithKeys(fn($line) => [
                    $line['product_id'] => [
                        'quantity' => $line['quantity'],
                        'price'    => $line['price'],
                    ],
                ])->toArray();
            $order->products()->sync($syncData);
    
            // ricalcolo e aggiorno il totale
            $total = collect($syncData)
                ->sum(fn($pivot) => $pivot['quantity'] * $pivot['price']);
            $order->update(['total' => $total]);
        }


        event(new OrderUpdated($order));
        return $order;
    }

    public function delete(int $id): bool
    {
        $order = $this->repo->find($id);
        $deleted = $this->repo->delete($order);
        if ($deleted) {
            event(new OrderDeleted($id));
        }
        return $deleted;
    }
}
