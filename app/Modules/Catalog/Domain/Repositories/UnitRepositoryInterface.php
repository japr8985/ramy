<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Models\Unit;
use Illuminate\Database\Eloquent\Collection;

interface UnitRepositoryInterface
{
    /**
     * Obtiene la lista completa de unidades registradas.
     *
     * @return Collection<int, Unit>
     */
    public function all(): Collection;

    /**
     * Busca una unidad por su UUID.
     */
    public function findById(string $id): ?Unit;

    /**
     * Crea y persiste una nueva unidad.
     *
     * @param array{name: string, symbol: string} $data
     */
    public function create(array $data): Unit;

    /**
     * Verifica si ya existe una unidad con el mismo símbolo o nombre.
     */
    public function existsBySymbolOrName(string $symbol, string $name): bool;
}