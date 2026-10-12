<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Exceptions\CannotDeleteProductWithSalesException;
use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeleteProductAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    /**
     * @throws CannotDeleteProductWithSalesException
     * @throws ModelNotFoundException
     */
    public function execute(string $id): void
    {
        $product = $this->repository->findByIdWithSalesCount($id);

        if (! $product) {
            throw new ModelNotFoundException("Producto con ID {$id} no encontrado.");
        }

        $salesCount = (int) ($product->sale_items_count ?? 0);

        if ($salesCount > 0) {
            throw CannotDeleteProductWithSalesException::hasHistoricalSales($salesCount);
        }

        $this->repository->delete($product);
    }
}