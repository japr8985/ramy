<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\Actions;

use App\Modules\Receivables\Domain\Models\AccountsReceivable;
use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;

final readonly class GetReceivableByIdAction
{
    public function __construct(
        private AccountsReceivableRepositoryInterface $repository,
    ) {}

    public function execute(string $id): ?AccountsReceivable
    {
        return $this->repository->findById($id);
    }
}