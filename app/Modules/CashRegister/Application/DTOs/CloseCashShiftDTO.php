<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\DTOs;

final readonly class CloseCashShiftDTO
{
    public function __construct(
        public string $shiftId,
        public float $actualCashUsd,
        public float $actualCashVes,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $shiftId, array $data): self
    {
        return new self(
            shiftId: $shiftId,
            actualCashUsd: (float) ($data['actual_cash_usd'] ?? 0.0),
            actualCashVes: (float) ($data['actual_cash_ves'] ?? 0.0),
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
        );
    }
}