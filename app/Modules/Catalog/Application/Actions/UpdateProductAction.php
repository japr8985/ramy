<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\UpdateProductDTO;
use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function execute(UpdateProductDTO $dto): Product
    {
        $product = $this->repository->findById($dto->id);

        if (! $product) {
            throw new ModelNotFoundException("Producto con ID {$dto->id} no encontrado.");
        }

        $this->repository->update($product, $dto->toArray());

        return $product->fresh(['category', 'unit']);
    }
}