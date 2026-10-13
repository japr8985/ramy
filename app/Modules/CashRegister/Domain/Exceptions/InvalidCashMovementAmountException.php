<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Exceptions;

use DomainException;

final class InvalidCashMovementAmountException extends DomainException
{
    public static function nonPositive(): self
    {
        return new self('El monto del movimiento de caja debe ser estrictamente mayor a 0.');
    }
}