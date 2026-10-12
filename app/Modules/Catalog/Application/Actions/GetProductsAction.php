<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetProductsAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function execute(
        ?string $search = null,
        ?string $categoryId = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate($search, $categoryId, $sortField, $sortDirection, $perPage);
    }
}