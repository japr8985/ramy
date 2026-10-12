<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetCategoriesAction
{
    public function __construct(
        private CategoryRepositoryInterface $repository,
    ) {}

    /**
     * Retorna listado paginado con búsqueda y conteo de productos.
     */
    public function paginate(
        ?string $search = null,
        string $sortField = 'name',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate($search, $sortField, $sortDirection, $perPage);
    }

    /**
     * Retorna todas las categorías para selects (sin paginar).
     *
     * @return Collection<int, \App\Modules\Catalog\Domain\Models\Category>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }
}