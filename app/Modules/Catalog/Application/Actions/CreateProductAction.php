<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\CreateProductDTO;
use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;

final readonly class CreateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        return $this->repository->create($dto->toArray());
    }
}