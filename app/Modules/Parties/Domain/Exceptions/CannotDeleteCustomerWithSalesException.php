<?php

declare(strict_types=1);

namespace App\Modules\Parties\Domain\Exceptions;

use DomainException;

final class CannotDeleteCustomerWithSalesException extends DomainException
{
    public static function hasHistoricalSales(int $salesCount): self
    {
        return new self("No se puede eliminar el cliente porque tiene {$salesCount} ventas registradas en el historial.");
    }
}