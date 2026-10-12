<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Exceptions\CannotDeleteCategoryWithProductsException;
use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeleteCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $repository,
    ) {}

    /**
     * @throws CannotDeleteCategoryWithProductsException
     * @throws ModelNotFoundException
     */
    public function execute(string $id): void
    {
        $category = $this->repository->findByIdWithProductsCount($id);

        if (! $category) {
            throw new ModelNotFoundException("Categoría con ID {$id} no encontrada.");
        }

        $productsCount = (int) ($category->products_count ?? 0);

        if ($productsCount > 0) {
            throw CannotDeleteCategoryWithProductsException::hasAssociatedProducts($productsCount);
        }

        $this->repository->delete($category);
    }
}