<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Purchasing\Domain\Repositories\PurchaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetPurchasesAction
{
    public function __construct(
        private PurchaseRepositoryInterface $repository,
    ) {}

    public function execute(
        ?string $search = null,
        ?string $supplierId = null,
        ?string $status = null,
        string $sortField = 'purchase_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate(
            search: $search,
            supplierId: $supplierId,
            status: $status,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );
    }
}