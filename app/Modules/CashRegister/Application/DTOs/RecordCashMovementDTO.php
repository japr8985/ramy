<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\DTOs;

final readonly class RecordCashMovementDTO
{
    public function __construct(
        public string $shiftId,
        public string $userId,
        public string $type,
        public string $currency,
        public float $amount,
        public string $concept,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $shiftId, string $userId, array $data): self
    {
        return new self(
            shiftId: $shiftId,
            userId: $userId,
            type: (string) ($data['type'] ?? 'out'),
            currency: strtoupper((string) ($data['currency'] ?? 'USD')),
            amount: (float) ($data['amount'] ?? 0.0),
            concept: trim((string) ($data['concept'] ?? '')),
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
            'cash_shift_id' => $this->shiftId,
            'user_id'       => $this->userId,
            'type'          => $this->type,
            'currency'      => $this->currency,
            'amount'        => $this->amount,
            'concept'       => $this->concept,
            'notes'         => $this->notes,
        ];
    }
}