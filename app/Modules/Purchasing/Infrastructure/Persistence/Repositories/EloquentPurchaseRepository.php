<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Persistence\Repositories;

use App\Modules\Purchasing\Domain\Models\Purchase;
use App\Modules\Purchasing\Domain\Repositories\PurchaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentPurchaseRepository implements PurchaseRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $supplierId = null,
        ?string $status = null,
        string $sortField = 'purchase_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['purchase_date', 'due_date', 'total', 'created_at'];
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'purchase_date';
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        return Purchase::query()
            ->with(['supplier:id,name,contact_person', 'creator:id,name'])
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'ilike', "%{$term}%")
                      ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'ilike', "%{$term}%"));
                });
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Purchase
    {
        return Purchase::query()
            ->with(['supplier', 'creator', 'items.product'])
            ->find($id);
    }

    public function recordAndReceive(array $purchaseData, array $itemsData): Purchase
    {
        /** @var Purchase $purchase */
        $purchase = Purchase::query()->create($purchaseData);
        $purchase->items()->createMany($itemsData);

        return $purchase->load(['supplier', 'items.product']);
    }
}