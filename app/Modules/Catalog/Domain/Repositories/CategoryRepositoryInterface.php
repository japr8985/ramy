<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Obtiene el listado paginado con filtros de búsqueda y conteo de productos.
     */
    public function paginate(
        ?string $search = null,
        string $sortField = 'name',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator;

    /**
     * Obtiene todas las categorías activas (útil para selects en formularios de productos).
     *
     * @return Collection<int, Category>
     */
    public function all(): Collection;

    public function findById(string $id): ?Category;

    public function findByIdWithProductsCount(string $id): ?Category;

    /**
     * @param array{name: string, description?: string|null, slug?: string} $data
     */
    public function create(array $data): Category;

    /**
     * @param array{name?: string, description?: string|null, slug?: string} $data
     */
    public function update(Category $category, array $data): bool;

    public function delete(Category $category): bool;
}