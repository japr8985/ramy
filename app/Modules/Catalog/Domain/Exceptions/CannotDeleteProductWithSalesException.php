<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Exceptions;

use DomainException;

final class CannotDeleteProductWithSalesException extends DomainException
{
    public static function hasHistoricalSales(int $salesCount): self
    {
        return new self("No se puede eliminar el producto porque tiene {$salesCount} ventas registradas en el historial.");
    }
}