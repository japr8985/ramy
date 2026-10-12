<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Repositories;

use App\Modules\Sales\Domain\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $status = null,
        ?string $paymentMethod = null,
        string $sortField = 'sale_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findById(string $id): ?Sale;

    /**
     * @param array<string, mixed> $saleData
     * @param array<int, array{product_id: string, quantity: int, price: float, subtotal: float}> $itemsData
     */
    public function createSaleWithItems(array $saleData, array $itemsData): Sale;
}