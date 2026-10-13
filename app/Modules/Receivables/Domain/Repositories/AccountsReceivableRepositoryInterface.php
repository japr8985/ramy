<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Repositories;

use App\Modules\Receivables\Domain\Models\AccountsReceivable;
use App\Modules\Receivables\Domain\Models\ReceivablePayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountsReceivableRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $customerId = null,
        ?string $status = null,
        bool $onlyWithBalance = true,
        string $sortField = 'due_date',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findById(string $id): ?AccountsReceivable;

    public function getStats(): array;

    /**
     * @param array<string, mixed> $paymentData
     */
    public function applyPayment(AccountsReceivable $receivable, array $paymentData, float $amountUsd): ReceivablePayment;
}