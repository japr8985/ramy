<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\UpdateCategoryDTO;
use App\Modules\Catalog\Domain\Models\Category;
use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpdateCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $repository,
    ) {}

    public function execute(UpdateCategoryDTO $dto): Category
    {
        $category = $this->repository->findById($dto->id);

        if (! $category) {
            throw new ModelNotFoundException("Categoría con ID {$dto->id} no encontrada.");
        }

        $this->repository->update($category, $dto->toArray());

        return $category->fresh();
    }
}