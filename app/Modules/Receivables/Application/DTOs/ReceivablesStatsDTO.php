<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\DTOs;

final readonly class ReceivablesStatsDTO
{
    public function __construct(
        public float $totalReceivables,
        public float $overdueAmount,
        public float $collectedMtd,
    ) {}

    /**
     * @param array{total_receivables: float, overdue_amount: float, collected_mtd: float} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            totalReceivables: round((float) ($data['total_receivables'] ?? 0.0), 4),
            overdueAmount: round((float) ($data['overdue_amount'] ?? 0.0), 4),
            collectedMtd: round((float) ($data['collected_mtd'] ?? 0.0), 4),
        );
    }

    /**
     * @return array{total_receivables: float, overdue_amount: float, collected_mtd: float}
     */
    public function toArray(): array
    {
        return [
            'total_receivables' => $this->totalReceivables,
            'overdue_amount'    => $this->overdueAmount,
            'collected_mtd'     => $this->collectedMtd,
        ];
    }
}