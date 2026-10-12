<?php

declare(strict_types=1);

namespace App\Modules\Sales\Infrastructure\Persistence\Repositories;

use App\Modules\Sales\Domain\Models\Sale;
use App\Modules\Sales\Domain\Repositories\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentSaleRepository implements SaleRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $status = null,
        ?string $paymentMethod = null,
        string $sortField = 'sale_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['sale_date', 'total', 'created_at', 'invoice_number'];
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'sale_date';

        return Sale::query()
            ->with(['customer:id,name,rif_ci', 'items.product:id,name,sku'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($paymentMethod, function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'ilike', "%{$term}%")
                      ->orWhereHas('customer', function ($cq) use ($term) {
                          $cq->where('name', 'ilike', "%{$term}%")
                             ->orWhere('rif_ci', 'ilike', "%{$term}%");
                      });
                });
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Sale
    {
        return Sale::query()
            ->with(['customer', 'items.product'])
            ->find($id);
    }

    public function createSaleWithItems(array $saleData, array $itemsData): Sale
    {
        /** @var Sale $sale */
        $sale = Sale::query()->create($saleData);

        $sale->items()->createMany($itemsData);

        return $sale->load(['customer', 'items.product']);
    }
}