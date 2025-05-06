<?php


namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Events\OrderDeleted;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $order = $this->repo->create($data);
        event(new OrderCreated($order));
        return $order;
    }

    public function update(int $id, array $data): Order
    {
        $order = $this->repo->find($id);
        $order = $this->repo->update($order, $data);
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
