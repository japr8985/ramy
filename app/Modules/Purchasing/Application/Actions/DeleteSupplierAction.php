<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeleteSupplierAction
{
    public function __construct(
        private SupplierRepositoryInterface $repository,
    ) {}

    public function execute(string $id): bool
    {
        $supplier = $this->repository->findById($id);

        if (! $supplier) {
            throw new ModelNotFoundException("Proveedor con ID '{$id}' no encontrado.");
        }

        return $this->repository->delete($supplier);
    }
}