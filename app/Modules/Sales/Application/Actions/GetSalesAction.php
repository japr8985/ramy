<?php

declare(strict_types=1);

namespace App\Modules\Sales\Application\Actions;

use App\Modules\Sales\Domain\Repositories\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetSalesAction
{
    public function __construct(
        private SaleRepositoryInterface $repository,
    ) {}

    public function execute(
        ?string $search = null,
        ?string $status = null,
        ?string $paymentMethod = null,
        string $sortField = 'sale_date',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate($search, $status, $paymentMethod, $sortField, $sortDirection, $perPage);
    }
}