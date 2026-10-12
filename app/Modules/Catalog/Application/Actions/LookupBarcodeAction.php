<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Services\BarcodeLookupServiceInterface;

final readonly class LookupBarcodeAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
        private BarcodeLookupServiceInterface $barcodeLookupService,
    ) {}

    /**
     * @return array{source: string, exists_locally: bool, data: array<string, mixed>|null}
     */
    public function execute(string $barcode): array
    {
        $barcode = trim($barcode);

        // 1. Verificar inventario local
        $localProduct = $this->repository->findBySku($barcode);
        if ($localProduct) {
            return [
                'source'         => 'local',
                'exists_locally' => true,
                'data'           => [
                    'id'            => $localProduct->id,
                    'sku'           => $localProduct->sku,
                    'name'          => $localProduct->name,
                    'category_id'   => $localProduct->category_id,
                    'unit_id'       => $localProduct->unit_id,
                    'selling_price' => $localProduct->selling_price,
                    'quantity'      => $localProduct->quantity,
                    'description'   => $localProduct->description,
                ],
            ];
        }

        // 2. Consulta al proveedor externo
        $externalData = $this->barcodeLookupService->lookup($barcode);
        if ($externalData) {
            return [
                'source'         => 'external',
                'exists_locally' => false,
                'data'           => [
                    'sku'         => $barcode,
                    'name'        => $externalData['name'],
                    'description' => $externalData['description'],
                ],
            ];
        }

        return [
            'source'         => 'none',
            'exists_locally' => false,
            'data'           => null,
        ];
    }
}