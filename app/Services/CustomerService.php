<?php

namespace App\Services;

use App\Contracts\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(protected CustomerRepositoryInterface $repo) {}

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

    public function get(int $id): ?Customer
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Customer
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Customer
    {
        $customer = $this->repo->find($id);
        return $this->repo->update($customer, $data);
    }

    public function delete(int $id): bool
    {
        $customer = $this->repo->find($id);
        return $this->repo->delete($customer);
    }
}
