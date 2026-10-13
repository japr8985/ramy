<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\Actions;

use App\Modules\CashRegister\Application\DTOs\RecordCashMovementDTO;
use App\Modules\CashRegister\Domain\Exceptions\InvalidCashMovementAmountException;
use App\Modules\CashRegister\Domain\Exceptions\NoActiveShiftFoundException;
use App\Modules\CashRegister\Domain\Exceptions\ShiftAlreadyClosedException;
use App\Modules\CashRegister\Domain\Models\CashMovement;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;

final readonly class RecordCashMovementAction
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
    ) {}

    /**
     * @throws NoActiveShiftFoundException
     * @throws ShiftAlreadyClosedException
     * @throws InvalidCashMovementAmountException
     */
    public function execute(RecordCashMovementDTO $dto): CashMovement
    {
        if ($dto->amount <= 0) {
            throw InvalidCashMovementAmountException::nonPositive();
        }

        $shift = $this->repository->findById($dto->shiftId);

        if (! $shift) {
            throw NoActiveShiftFoundException::forId($dto->shiftId);
        }

        if ($shift->isClosed()) {
            throw ShiftAlreadyClosedException::forShift($shift->id);
        }

        return $this->repository->createMovement($dto->toArray());
    }
}