<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Exceptions;

use DomainException;

final class NoActiveShiftFoundException extends DomainException
{
    public static function forUser(string $name): self
    {
        return new self("No se encontró ningún turno de caja activo para el usuario: '{$name}'.");
    }

    public static function forId(string $shiftId): self
    {
        return new self("No se encontró ningún turno de caja activo con ID '{$shiftId}'.");
    }
}