<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\CreateCategoryDTO;
use App\Modules\Catalog\Domain\Models\Category;
use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;

final readonly class CreateCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $repository,
    ) {}

    public function execute(CreateCategoryDTO $dto): Category
    {
        return $this->repository->create($dto->toArray());
    }
}