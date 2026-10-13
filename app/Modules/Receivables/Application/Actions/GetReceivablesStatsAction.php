<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\Actions;

use App\Modules\Receivables\Application\DTOs\ReceivablesStatsDTO;
use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;

final readonly class GetReceivablesStatsAction
{
    public function __construct(
        private AccountsReceivableRepositoryInterface $repository,
    ) {}

    public function execute(): ReceivablesStatsDTO
    {
        $stats = $this->repository->getStats();

        return ReceivablesStatsDTO::fromArray($stats);
    }
}