<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->list($filters, $perPage);
    }

    public function list(
        array $filters = [],
        int $perPage = 15,
        string $sortField = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        $query = Product::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        $query = Product::query();
        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }
        return $query->get();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }
}
