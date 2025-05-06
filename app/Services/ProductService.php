<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $repo) {}

    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        return $this->repo->list($filters, $perPage, $sortField, $sortDirection);
    }

    public function all(array $filters = []): \Illuminate\Support\Collection
    {
        return $this->repo->all($filters);
    }

    public function get(int $id): ?Product
    {
        return $this->repo->find($id);
    }
}
