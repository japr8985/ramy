<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Purchasing\Domain\Models\Purchase;
use App\Modules\Purchasing\Domain\Repositories\PurchaseRepositoryInterface;

final readonly class GetPurchaseByIdAction
{
    public function __construct(
        private PurchaseRepositoryInterface $repository,
    ) {}

    public function execute(string $id): ?Purchase
    {
        return $this->repository->findById($id);
    }
}