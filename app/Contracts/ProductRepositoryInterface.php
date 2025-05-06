<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Product;
    public function all(array $filters = []): Collection;
    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator;
}
