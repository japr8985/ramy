<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Repositories;

use App\Modules\Purchasing\Domain\Models\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $supplierId = null,
        ?string $status = null,
        string $sortField = 'purchase_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findById(string $id): ?Purchase;

    /**
     * Crea y aplica atómicamente la recepción de mercancía e incremento de existencias en catálogo.
     *
     * @param array<string, mixed> $purchaseData
     * @param array<int, array<string, mixed>> $itemsData
     */
    public function recordAndReceive(array $purchaseData, array $itemsData): Purchase;
}