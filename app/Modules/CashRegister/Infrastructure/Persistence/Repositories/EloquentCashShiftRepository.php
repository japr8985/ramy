<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Persistence\Repositories;

use App\Modules\CashRegister\Domain\Models\CashMovement;
use App\Modules\CashRegister\Domain\Models\CashShift;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;
use App\Modules\Sales\Domain\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentCashShiftRepository implements CashShiftRepositoryInterface
{
    public function paginate(
        ?string $userId = null,
        ?string $status = null,
        string $sortField = 'opened_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['opened_at', 'closed_at', 'created_at'];
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'opened_at';

        return CashShift::query()
            ->with(['user:id,name,email'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function findById(string $id): ?CashShift
    {
        return CashShift::query()
            ->with(['user', 'movements.user', 'sales'])
            ->find($id);
    }

    public function findOpenShiftByUserId(string $userId): ?CashShift
    {
        return CashShift::query()
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }

    public function createShift(array $data): CashShift
    {
        return CashShift::query()->create($data);
    }

    public function updateShift(CashShift $shift, array $data): bool
    {
        return $shift->update($data);
    }

    public function createMovement(array $data): CashMovement
    {
        return CashMovement::query()->create($data);
    }

    public function calculateShiftTotals(CashShift $shift): array
    {
        // 1. Ventas en efectivo asociadas al turno (cash_received en USD)
        $cashSalesUsd = (float) Sale::query()
            ->where('cash_shift_id', $shift->id)
            ->where('status', 'completed')
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                  ->orWhere(fn ($sq) => $sq->where('payment_method', 'credit')->where('cash_received', '>', 0));
            })
            ->sum('cash_received');

        // Para VES, proyectamos según la tasa congelada en cada venta
        $cashSalesVes = (float) Sale::query()
            ->where('cash_shift_id', $shift->id)
            ->where('status', 'completed')
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                  ->orWhere(fn ($sq) => $sq->where('payment_method', 'credit')->where('cash_received', '>', 0));
            })
            ->selectRaw('SUM(cash_received * exchange_rate) as total_ves')
            ->value('total_ves') ?? 0.0;

        // 2. Movimientos manuales: Ingresos (in)
        $manualInUsd = (float) CashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'in')
            ->where('currency', 'USD')
            ->sum('amount');

        $manualInVes = (float) CashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'in')
            ->where('currency', 'VES')
            ->sum('amount');

        // 3. Movimientos manuales: Egresos / Gastos (out)
        $manualOutUsd = (float) CashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'out')
            ->where('currency', 'USD')
            ->sum('amount');

        $manualOutVes = (float) CashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'out')
            ->where('currency', 'VES')
            ->sum('amount');

        // 4. Totales esperados en caja = Base + Ventas + Entradas - Salidas
        $expectedUsd = (float) $shift->initial_cash_usd + $cashSalesUsd + $manualInUsd - $manualOutUsd;
        $expectedVes = (float) $shift->initial_cash_ves + $cashSalesVes + $manualInVes - $manualOutVes;

        return [
            'initial_usd'    => (float) $shift->initial_cash_usd,
            'initial_ves'    => (float) $shift->initial_cash_ves,
            'cash_sales_usd' => round($cashSalesUsd, 4),
            'cash_sales_ves' => round($cashSalesVes, 4),
            'manual_in_usd'  => round($manualInUsd, 4),
            'manual_in_ves'  => round($manualInVes, 4),
            'manual_out_usd' => round($manualOutUsd, 4),
            'manual_out_ves' => round($manualOutVes, 4),
            'expected_usd'   => round(max(0.0, $expectedUsd), 4),
            'expected_ves'   => round(max(0.0, $expectedVes), 4),
        ];
    }
}