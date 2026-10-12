<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Exceptions;

use DomainException;

final class CannotDeleteCategoryWithProductsException extends DomainException
{
    public static function hasAssociatedProducts(int $count): self
    {
        return new self("No puedes eliminar una categoría que contiene {$count} productos asociados.");
    }
}