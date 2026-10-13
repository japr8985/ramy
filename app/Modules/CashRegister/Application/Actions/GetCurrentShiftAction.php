<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\Actions;

use App\Modules\CashRegister\Domain\Models\CashShift;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;

final readonly class GetCurrentShiftAction
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
    ) {}

    /**
     * Obtiene el turno abierto del usuario actual, opcionalmente con sus totales calculados en caliente.
     *
     * @return array{shift: CashShift, summary: array<string, float>}|null
     */
    public function execute(string $userId): ?array
    {
        $shift = $this->repository->findOpenShiftByUserId($userId);

        if (! $shift) {
            return null;
        }

        $summary = $this->repository->calculateShiftTotals($shift);

        return [
            'shift'   => $shift->load(['movements' => fn ($q) => $q->latest()->take(10)]),
            'summary' => $summary,
        ];
    }
}