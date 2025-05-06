<?php

namespace App\Contracts;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Customer;
    public function create(array $data): Customer;
    public function update(Customer $customer, array $data): Customer;
    public function delete(Customer $customer): bool;
    public function all(array $filters = []): Collection;
    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
