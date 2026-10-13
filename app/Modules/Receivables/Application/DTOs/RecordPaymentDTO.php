<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\DTOs;

final readonly class RecordPaymentDTO
{
    public function __construct(
        public string $receivableId,
        public float $amountUsd,
        public float $exchangeRate,
        public string $paymentDate,
        public string $paymentMethod,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $receivableId, array $data): self
    {
        return new self(
            receivableId: $receivableId,
            amountUsd: round((float) ($data['amount_usd'] ?? 0.0), 4),
            exchangeRate: round((float) ($data['exchange_rate'] ?? 1.0), 4),
            paymentDate: (string) ($data['payment_date'] ?? now()->toDateString()),
            paymentMethod: (string) ($data['payment_method'] ?? 'cash'),
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPaymentArray(): array
    {
        return [
            'accounts_receivable_id' => $this->receivableId,
            'payment_date'           => $this->paymentDate,
            'amount_usd'             => $this->amountUsd,
            'exchange_rate'          => $this->exchangeRate,
            'payment_method'         => $this->paymentMethod,
            'notes'                  => $this->notes,
        ];
    }
}