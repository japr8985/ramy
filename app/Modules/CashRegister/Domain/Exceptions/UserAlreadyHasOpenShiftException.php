<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Exceptions;

use DomainException;

final class UserAlreadyHasOpenShiftException extends DomainException
{
    public static function forUser(string $name): self
    {
        return new self("El usuario con ID '{$name}' ya tiene un turno de caja abierto.");
    }
}