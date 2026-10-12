<?php

declare(strict_types=1);

namespace App\Modules\Sales\Application\DTOs;

final readonly class ProcessSaleItemDTO
{
    public function __construct(
        public string $productId,
        public int $quantity,
        public float $price,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (string) ($data['product_id'] ?? $data['id']),
            quantity: (int) ($data['quantity'] ?? $data['qty'] ?? 1),
            price: (float) $data['price'],
        );
    }

    public function subtotal(): float
    {
        return round($this->price * $this->quantity, 4);
    }

    /**
     * @return array{product_id: string, quantity: int, price: float, subtotal: float}
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity'   => $this->quantity,
            'price'      => $this->price,
            'subtotal'   => $this->subtotal(),
        ];
    }
}