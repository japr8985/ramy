<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

final readonly class UpdateProductDTO
{
    public function __construct(
        public string $id,
        public string $sku,
        public string $name,
        public string $categoryId,
        public string $unitId,
        public float $purchasePrice,
        public float $sellingPrice,
        public int $quantity,
        public int $minStock,
        public bool $isActive = true,
        public ?string $description = null,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            sku: trim((string) ($data['sku'] ?? $data['barcode'])),
            name: trim((string) $data['name']),
            categoryId: (string) $data['category_id'],
            unitId: (string) $data['unit_id'],
            purchasePrice: (float) $data['purchase_price'],
            sellingPrice: (float) $data['selling_price'],
            quantity: (int) $data['quantity'],
            minStock: (int) $data['min_stock'],
            isActive: (bool) ($data['is_active'] ?? true),
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sku'            => $this->sku,
            'name'           => $this->name,
            'category_id'    => $this->categoryId,
            'unit_id'        => $this->unitId,
            'purchase_price' => $this->purchasePrice,
            'selling_price'  => $this->sellingPrice,
            'quantity'       => $this->quantity,
            'min_stock'      => $this->minStock,
            'is_active'      => $this->isActive,
            'description'    => $this->description,
            'notes'          => $this->notes,
        ];
    }
}