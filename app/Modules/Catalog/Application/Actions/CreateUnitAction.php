<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\CreateUnitDTO;
use App\Modules\Catalog\Domain\Models\Unit;
use App\Modules\Catalog\Domain\Repositories\UnitRepositoryInterface;

final readonly class CreateUnitAction
{
    public function __construct(
        private UnitRepositoryInterface $repository,
    ) {}

    public function execute(CreateUnitDTO $dto): Unit
    {
        return $this->repository->create($dto->toArray());
    }
}