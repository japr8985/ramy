<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Exceptions;

use DomainException;

final class EmptySaleCartException extends DomainException
{
    public static function create(): self
    {
        return new self('El carrito de compras no contiene productos.');
    }
}