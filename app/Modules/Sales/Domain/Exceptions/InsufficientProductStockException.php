<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Exceptions;

use DomainException;

final class InsufficientProductStockException extends DomainException
{
    public static function forProduct(string $productName, int $availableStock, int $requestedQuantity): self
    {
        return new self("Stock insuficiente para '{$productName}'. Disponible: {$availableStock}, solicitado: {$requestedQuantity}.");
    }
}