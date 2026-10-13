<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\Actions;

use App\Modules\CashRegister\Domain\Exceptions\NoActiveShiftFoundException;
use App\Modules\CashRegister\Domain\Models\CashShift;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;

final readonly class GetShiftSummaryAction
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
    ) {}

    /**
     * @return array{shift: CashShift, summary: array<string, float>}
     *
     * @throws NoActiveShiftFoundException
     */
    public function execute(string $shiftId): array
    {
        $shift = $this->repository->findById($shiftId);

        if (! $shift) {
            throw NoActiveShiftFoundException::forId($shiftId);
        }

        $summary = $this->repository->calculateShiftTotals($shift);

        return [
            'shift'   => $shift->load('movements'),
            'summary' => $summary,
        ];
    }
}