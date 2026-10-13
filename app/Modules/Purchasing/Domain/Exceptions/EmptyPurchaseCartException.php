<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Exceptions;

use DomainException;

final class EmptyPurchaseCartException extends DomainException
{
    public static function create(): self
    {
        return new self('La orden de compra debe contener al menos un producto.');
    }
}