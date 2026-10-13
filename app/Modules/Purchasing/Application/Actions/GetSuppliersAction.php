<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetSuppliersAction
{
    public function __construct(
        private SupplierRepositoryInterface $repository,
    ) {}

    public function execute(
        ?string $search = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate(
            search: $search,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );
    }
}