<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Exceptions;

use DomainException;

final class ShiftAlreadyClosedException extends DomainException
{
    public static function forShift(string $shiftId): self
    {
        return new self("El turno de caja con ID '{$shiftId}' ya se encuentra cerrado.");
    }
}