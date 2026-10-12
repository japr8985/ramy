<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Services;

interface BarcodeLookupServiceInterface
{
    /**
     * Busca información descriptiva de un producto en base a su código de barras.
     *
     * @return array{name: string, description: string}|null
     */
    public function lookup(string $barcode): ?array;
}