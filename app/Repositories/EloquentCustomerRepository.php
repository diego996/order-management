<?php

namespace App\Repositories;

use App\Contracts\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->list($filters, $perPage);
    }

    public function list(
        array  $filters = [],
        int    $perPage = 15,
        string $sortField = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator
    {
        $query = Customer::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $query = Customer::query();
        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }
        return $query->get();
    }

    public function find(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer;
    }

    public function delete(Customer $customer): bool
    {
        return (bool)$customer->delete();
    }
}
