<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\Actions;

use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetReceivablesAction
{
    public function __construct(
        private AccountsReceivableRepositoryInterface $repository,
    ) {}

    public function execute(
        ?string $search = null,
        ?string $customerId = null,
        ?string $status = null,
        bool $onlyWithBalance = true,
        string $sortField = 'due_date',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate(
            search: $search,
            customerId: $customerId,
            status: $status,
            onlyWithBalance: $onlyWithBalance,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );
    }
}