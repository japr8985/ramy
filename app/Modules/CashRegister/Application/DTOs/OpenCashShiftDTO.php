<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\DTOs;

final readonly class OpenCashShiftDTO
{
    public function __construct(
        public string $userId,
        public float $initialCashUsd = 0.0,
        public float $initialCashVes = 0.0,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $userId, array $data): self
    {
        return new self(
            userId: $userId,
            initialCashUsd: max(0.0, (float) ($data['initial_cash_usd'] ?? 0.0)),
            initialCashVes: max(0.0, (float) ($data['initial_cash_ves'] ?? 0.0)),
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
            'user_id'          => $this->userId,
            'status'           => 'open',
            'opened_at'        => now(),
            'initial_cash_usd' => $this->initialCashUsd,
            'initial_cash_ves' => $this->initialCashVes,
            'notes'            => $this->notes,
        ];
    }
}