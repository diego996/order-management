<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Order;
    public function create(array $data): Order;
    public function update(Order $order, array $data): Order;
    public function delete(Order $order): bool;
    public function all(array $filters = []): Collection;
    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
