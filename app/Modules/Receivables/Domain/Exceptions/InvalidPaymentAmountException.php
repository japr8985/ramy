<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Exceptions;

use DomainException;

final class InvalidPaymentAmountException extends DomainException
{
    public static function nonPositive(): self
    {
        return new self('El monto del abono debe ser estrictamente mayor a 0.');
    }
}