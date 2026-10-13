<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Exceptions;

use DomainException;

final class PurchaseAlreadyReceivedException extends DomainException
{
    public static function forId(string $id): self
    {
        return new self("La compra con ID '{$id}' ya fue recibida e ingresada al inventario.");
    }
}