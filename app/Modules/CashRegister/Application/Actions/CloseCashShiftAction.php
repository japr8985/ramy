<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\Actions;

use App\Modules\CashRegister\Application\DTOs\CloseCashShiftDTO;
use App\Modules\CashRegister\Domain\Exceptions\NoActiveShiftFoundException;
use App\Modules\CashRegister\Domain\Exceptions\ShiftAlreadyClosedException;
use App\Modules\CashRegister\Domain\Models\CashShift;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;
use Illuminate\Support\Facades\DB;

final readonly class CloseCashShiftAction
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
    ) {}

    /**
     * @throws NoActiveShiftFoundException
     * @throws ShiftAlreadyClosedException
     */
    public function execute(CloseCashShiftDTO $dto): CashShift
    {
        $shift = $this->repository->findById($dto->shiftId);

        if (! $shift) {
            throw NoActiveShiftFoundException::forId($dto->shiftId);
        }

        if ($shift->isClosed()) {
            throw ShiftAlreadyClosedException::forShift($shift->id);
        }

        return DB::transaction(function () use ($shift, $dto): CashShift {
            // Totales acumulados esperados al momento del cierre
            $totals = $this->repository->calculateShiftTotals($shift);

            $expectedUsd = $totals['expected_usd'];
            $expectedVes = $totals['expected_ves'];

            // Diferencia: Contado Real - Esperado (Positivo = Sobrante, Negativo = Faltante)
            $differenceUsd = round($dto->actualCashUsd - $expectedUsd, 4);
            $differenceVes = round($dto->actualCashVes - $expectedVes, 4);

            $closingData = [
                'status'            => 'closed',
                'closed_at'         => now(),
                'expected_cash_usd' => $expectedUsd,
                'expected_cash_ves' => $expectedVes,
                'actual_cash_usd'   => $dto->actualCashUsd,
                'actual_cash_ves'   => $dto->actualCashVes,
                'difference_usd'    => $differenceUsd,
                'difference_ves'    => $differenceVes,
                'notes'             => $dto->notes ?: $shift->notes,
            ];

            $this->repository->updateShift($shift, $closingData);

            return $shift->fresh(['user', 'movements']);
        });
    }
}