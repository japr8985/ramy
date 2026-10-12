<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Repositories;

use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $categoryId = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['name', 'sku', 'selling_price', 'quantity', 'created_at'];
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'created_at';

        return Product::query()
            ->with(['category:id,name,slug', 'unit:id,name,symbol'])
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'ilike', "%{$term}%")
                      ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->when($categoryId, function ($query, $catId) {
                $query->where('category_id', $catId);
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function getInventoryKpis(): array
    {
        $totalProducts = Product::query()->count('id');

        $criticalStock = Product::query()
            ->whereColumn('quantity', '<', 'min_stock')
            ->count('id');

        $stockValue = (float) (Product::query()
            ->where('quantity', '>', 0)
            ->selectRaw('COALESCE(SUM(selling_price * quantity), 0) as total_value')
            ->value('total_value') ?? 0.0);

        return [
            'total_products' => $totalProducts,
            'critical_stock' => $criticalStock,
            'stock_value'    => round($stockValue, 2),
        ];
    }

    public function findById(string $id): ?Product
    {
        return Product::query()
            ->with(['category', 'unit'])
            ->find($id);
    }

    public function findByIdWithSalesCount(string $id): ?Product
    {
        return Product::query()
            ->withCount('saleItems')
            ->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::query()
            ->with(['category', 'unit'])
            ->where('sku', $sku)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}