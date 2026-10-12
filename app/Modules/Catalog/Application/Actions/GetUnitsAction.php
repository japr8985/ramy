<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Domain\Models\Unit;
use App\Modules\Catalog\Domain\Repositories\UnitRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetUnitsAction
{
    public function __construct(
        private UnitRepositoryInterface $repository,
    ) {}

    /**
     * @return Collection<int, Unit>
     */
    public function execute(): Collection
    {
        return $this->repository->all();
    }
}