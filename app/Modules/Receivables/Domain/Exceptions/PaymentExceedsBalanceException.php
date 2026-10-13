<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Exceptions;

use DomainException;

final class PaymentExceedsBalanceException extends DomainException
{
    public static function withAmounts(float $attempted, float $balance): self
    {
        return new self("El monto a abonar (\${$attempted}) excede el saldo deudor pendiente (\${$balance}).");
    }
}