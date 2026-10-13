<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Purchasing\Application\DTOs\SaveSupplierDTO;
use App\Modules\Purchasing\Domain\Models\Supplier;
use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class SaveSupplierAction
{
    public function __construct(
        private SupplierRepositoryInterface $repository,
    ) {}

    public function execute(SaveSupplierDTO $dto, ?string $id = null): Supplier
    {
        if ($id) {
            $supplier = $this->repository->findById($id);

            if (! $supplier) {
                throw new ModelNotFoundException("Proveedor con ID '{$id}' no encontrado.");
            }

            $this->repository->update($supplier, $dto->toArray(), $dto->productIds);

            return $supplier->fresh(['products']);
        }

        return $this->repository->create($dto->toArray(), $dto->productIds);
    }
}