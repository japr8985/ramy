<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Services;

use App\Modules\Catalog\Domain\Services\BarcodeLookupServiceInterface;
use Illuminate\Support\Facades\Http;
use Throwable;

final class OpenFoodFactsBarcodeLookupService implements BarcodeLookupServiceInterface
{
    private const BASE_URL = 'https://world.openfoodfacts.org/api/v0/product';

    public function lookup(string $barcode): ?array
    {
        try {
            $response = Http::timeout(4)
                ->acceptJson()
                ->get(sprintf('%s/%s.json', self::BASE_URL, urlencode($barcode)));

            if (! $response->successful() || (int) $response->json('status') !== 1) {
                return null;
            }

            $productData = $response->json('product', []);

            $name = $productData['product_name'] 
                ?? $productData['generic_name'] 
                ?? null;

            if (empty($name)) {
                return null;
            }

            $brands = $productData['brands'] ?? 'Importado vía OpenFoodFacts';

            return [
                'name'        => trim((string) $name),
                'description' => trim((string) $brands),
            ];
        } catch (Throwable) {
            return null;
        }
    }
}