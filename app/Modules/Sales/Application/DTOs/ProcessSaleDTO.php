<?php

declare(strict_types=1);

namespace App\Modules\Sales\Application\DTOs;

final readonly class ProcessSaleDTO
{
    /**
     * @param array<int, ProcessSaleItemDTO> $items
     */
    public function __construct(
        public array $items,
        public ?string $customerId = null,
        public ?string $userId = null,
        public ?string $cashShiftId = null,
        public string $paymentMethod = 'cash',
        public float $totalDiscount = 0.0,
        public float $exchangeRate = 1.0,
        public float $initialPayment = 0.0,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawItems = (array) ($data['items'] ?? $data['cart'] ?? []);
        $items = array_map(
            fn (array $item) => ProcessSaleItemDTO::fromArray($item),
            $rawItems
        );

        return new self(
            items: $items,
            customerId: isset($data['customer_id']) && trim((string) $data['customer_id']) !== ''
                ? trim((string) $data['customer_id'])
                : null,
            userId: isset($data['created_by']) ? (string) $data['created_by'] : null,
            cashShiftId: isset($data['cash_shift_id']) && trim((string) $data['cash_shift_id']) !== ''
                ? trim((string) $data['cash_shift_id'])
                : null,
            paymentMethod: (string) ($data['payment_method'] ?? 'cash'),
            totalDiscount: (float) ($data['total_discount'] ?? 0.0),
            exchangeRate: (float) ($data['exchange_rate'] ?? 1.0),
            initialPayment: (float) ($data['initial_payment'] ?? 0.0),
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
        );
    }

    public function calculateSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += $item->subtotal();
        }
        return round($subtotal, 4);
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $discount = min($this->totalDiscount, $subtotal);
        return round(max(0.0, $subtotal - $discount), 4);
    }
}