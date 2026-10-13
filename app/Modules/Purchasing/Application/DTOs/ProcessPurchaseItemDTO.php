<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\DTOs;

final readonly class ProcessPurchaseItemDTO
{
    public function __construct(
        public string $productId,
        public int $quantity,
        public float $unitPrice,
        public ?float $sellingPrice = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (string) $data['product_id'],
            quantity: max(1, (int) ($data['quantity'] ?? 1)),
            unitPrice: round(max(0.0, (float) ($data['unit_price'] ?? 0.0)), 4),
            sellingPrice: isset($data['selling_price']) && (float) $data['selling_price'] > 0
                ? round((float) $data['selling_price'], 4)
                : null,
        );
    }

    public function subtotal(): float
    {
        return round($this->quantity * $this->unitPrice, 4);
    }

    public function toArray(): array
    {
        return [
            'product_id'    => $this->productId,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->unitPrice,
            'selling_price' => $this->sellingPrice,
            'subtotal'      => $this->subtotal(),
        ];
    }
}