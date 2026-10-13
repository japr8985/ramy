<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Infrastructure\Persistence\Repositories;

use App\Modules\Receivables\Domain\Models\AccountsReceivable;
use App\Modules\Receivables\Domain\Models\ReceivablePayment;
use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class EloquentAccountsReceivableRepository implements AccountsReceivableRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $customerId = null,
        ?string $status = null,
        bool $onlyWithBalance = true,
        string $sortField = 'due_date',
        string $sortDirection = 'asc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['due_date', 'sale_date', 'total', 'balance', 'created_at'];
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'due_date';
        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        return AccountsReceivable::query()
            ->with([
                'customer:id,name,rif_ci,phone',
                'sale:id,invoice_number,total,sale_date',
            ])
            ->when($onlyWithBalance, fn ($q) => $q->where('balance', '>', 0))
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when($status, function ($query, $status) {
                if ($status === 'paid') {
                    $query->where('balance', '<=', 0);
                } elseif ($status === 'partial') {
                    $query->where('balance', '>', 0)->where('paid_amount', '>', 0);
                } elseif ($status === 'pending') {
                    $query->where('balance', '>', 0)->where('paid_amount', '<=', 0);
                } elseif ($status === 'overdue') {
                    $query->where('balance', '>', 0)->where('due_date', '<', now());
                }
            })
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->whereHas('customer', function ($cq) use ($term) {
                        $cq->where('name', 'ilike', "%{$term}%")
                           ->orWhere('rif_ci', 'ilike', "%{$term}%");
                    })->orWhereHas('sale', function ($sq) use ($term) {
                        $sq->where('invoice_number', 'ilike', "%{$term}%");
                    });
                });
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function findById(string $id): ?AccountsReceivable
    {
        return AccountsReceivable::query()
            ->with([
                'customer',
                'sale',
                'items.product:id,name,sku',
                'payments' => fn ($q) => $q->latest('payment_date'),
            ])
            ->find($id);
    }

    public function getStats(): array
    {
        $totalReceivables = (float) AccountsReceivable::query()->sum('balance');
        $overdueAmount = (float) AccountsReceivable::query()
            ->where('balance', '>', 0)
            ->where('due_date', '<', now())
            ->sum('balance');

        $collectedMtd = (float) ReceivablePayment::query()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_usd');

        return [
            'total_receivables' => round($totalReceivables, 4),
            'overdue_amount'    => round($overdueAmount, 4),
            'collected_mtd'     => round($collectedMtd, 4),
        ];
    }

    public function applyPayment(
        AccountsReceivable $receivable,
        array $paymentData,
        float $amountUsd
    ): ReceivablePayment {
        return DB::transaction(function () use ($receivable, $paymentData, $amountUsd) {
            /** @var ReceivablePayment $payment */
            $payment = ReceivablePayment::query()->create($paymentData);

            $receivable->increment('paid_amount', $amountUsd);
            $receivable->decrement('balance', $amountUsd);

            return $payment;
        });
    }
}