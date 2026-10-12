<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;

final readonly class GetProductKpisAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    /**
     * @return array{total_products: int, critical_stock: int, stock_value: float}
     */
    public function execute(): array
    {
        return $this->repository->getInventoryKpis();
    }
}