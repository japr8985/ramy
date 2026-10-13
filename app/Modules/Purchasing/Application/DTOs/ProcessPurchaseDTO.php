<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\DTOs;

final readonly class ProcessPurchaseDTO
{
    /**
     * @param array<int, ProcessPurchaseItemDTO> $items
     */
    public function __construct(
        public string $supplierId,
        public string $purchaseDate,
        public array $items,
        public ?string $invoiceNumber = null,
        public ?string $dueDate = null,
        public ?string $notes = null,
        public ?string $proofImage = null,
        public ?string $createdBy = null,
    ) {}

    public static function fromArray(array $data, ?string $userId = null): self
    {
        $rawItems = (array) ($data['items'] ?? []);
        $items = array_map(fn (array $item) => ProcessPurchaseItemDTO::fromArray($item), $rawItems);

        return new self(
            supplierId: (string) $data['supplier_id'],
            purchaseDate: (string) ($data['purchase_date'] ?? now()->toDateString()),
            items: $items,
            invoiceNumber: isset($data['invoice_number']) && trim((string) $data['invoice_number']) !== ''
                ? trim((string) $data['invoice_number'])
                : null,
            dueDate: isset($data['due_date']) && trim((string) $data['due_date']) !== ''
                ? trim((string) $data['due_date'])
                : null,
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
            proofImage: $data['proof_image'] ?? null,
            createdBy: $userId ?? (isset($data['created_by']) ? (string) $data['created_by'] : null),
        );
    }

    public function calculateTotal(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->subtotal();
        }
        return round($total, 2);
    }
}