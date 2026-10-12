<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Repositories;

use App\Modules\Catalog\Domain\Models\Category;
use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        string $sortField = 'name',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['name', 'created_at'];
        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'name';

        return Category::query()
            ->withCount('products')
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Category::query()
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findById(string $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function findByIdWithProductsCount(string $id): ?Category
    {
        return Category::query()
            ->withCount('products')
            ->find($id);
    }

    public function create(array $data): Category
    {
        return Category::query()->create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}