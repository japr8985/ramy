<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Obtiene productos paginados con filtros por búsqueda, categoría, stock crítico y orden.
     */
    public function paginate(
        ?string $search = null,
        ?string $categoryId = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    /**
     * Retorna indicadores clave de inventario (total productos, stock crítico, valor total del stock).
     *
     * @return array{total_products: int, critical_stock: int, stock_value: float}
     */
    public function getInventoryKpis(): array;

    public function findById(string $id): ?Product;

    public function findByIdWithSalesCount(string $id): ?Product;

    public function findBySku(string $sku): ?Product;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Product;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Product $product, array $data): bool;

    public function delete(Product $product): bool;
}