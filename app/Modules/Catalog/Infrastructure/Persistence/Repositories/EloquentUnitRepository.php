<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence\Repositories;

use App\Modules\Catalog\Domain\Models\Unit;
use App\Modules\Catalog\Domain\Repositories\UnitRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUnitRepository implements UnitRepositoryInterface
{
    public function all(): Collection
    {
        return Unit::query()
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findById(string $id): ?Unit
    {
        return Unit::query()->find($id);
    }

    public function create(array $data): Unit
    {
        return Unit::query()->create($data);
    }

    public function existsBySymbolOrName(string $symbol, string $name): bool
    {
        return Unit::query()
            ->where('symbol', $symbol)
            ->orWhere('name', $name)
            ->exists();
    }
}