<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Exceptions;

use DomainException;

final class ReceivableAlreadySettledException extends DomainException
{
    public static function forId(string $id): self
    {
        return new self("La cuenta por cobrar con ID '{$id}' ya está completamente liquidada.");
    }
}