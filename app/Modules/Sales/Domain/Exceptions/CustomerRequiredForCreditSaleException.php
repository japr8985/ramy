<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Exceptions;

use DomainException;

final class CustomerRequiredForCreditSaleException extends DomainException
{
    public static function create(): self
    {
        return new self('Las ventas a crédito deben asociarse obligatoriamente a un cliente registrado.');
    }
}