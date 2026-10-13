<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Persistence\Repositories;

use App\Modules\Purchasing\Domain\Models\Supplier;
use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['name', 'contact_person', 'email', 'phone', 'created_at'];
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'created_at';
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        return Supplier::query()
            ->with(['products:id,name,sku'])
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'ilike', "%{$term}%")
                      ->orWhere('contact_person', 'ilike', "%{$term}%")
                      ->orWhere('email', 'ilike', "%{$term}%")
                      ->orWhere('phone', 'ilike', "%{$term}%");
                });
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Supplier::query()->orderBy('name')->get();
    }

    public function findById(string $id): ?Supplier
    {
        return Supplier::query()->with(['products'])->find($id);
    }

    public function create(array $data, array $productIds = []): Supplier
    {
        return DB::transaction(function () use ($data, $productIds) {
            /** @var Supplier $supplier */
            $supplier = Supplier::query()->create($data);

            if (! empty($productIds)) {
                $supplier->products()->sync($productIds);
            }

            return $supplier->load('products');
        });
    }

    public function update(Supplier $supplier, array $data, ?array $productIds = null): bool
    {
        return DB::transaction(function () use ($supplier, $data, $productIds) {
            $updated = $supplier->update($data);

            if ($productIds !== null) {
                $supplier->products()->sync($productIds);
            }

            return $updated;
        });
    }

    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }

    public function getStats(): array
    {
        $totalSuppliers = Supplier::query()->count();
        $totalProductsLinked = DB::table('product_supplier')->distinct('product_id')->count('product_id');

        return [
            'total_suppliers'       => $totalSuppliers,
            'total_products_linked' => $totalProductsLinked,
        ];
    }
}