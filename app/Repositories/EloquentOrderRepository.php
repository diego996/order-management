<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->list($filters, $perPage);
    }

    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Order::query();

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {

            $query->whereDate('order_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('order_date', '<=', $filters['date_to']);
        }
        if ($sortField === 'customer_name') {
            // faccio il join per ordinare sulla colonna customers.name
            $query->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->orderBy('customers.name', $sortDirection)
                ->select('orders.*');
            return $query
                ->paginate($perPage);
        }
        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $query = Order::query();
        // eventuali filtri simili a list()
        return $query->get();
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }

    public function delete(Order $order): bool
    {
        return (bool) $order->delete();
    }
}
