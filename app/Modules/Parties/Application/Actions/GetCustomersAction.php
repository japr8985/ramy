<?php

declare(strict_types=1);

namespace App\Modules\Parties\Application\Actions;

use App\Modules\Parties\Domain\Models\Customer;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetCustomersAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
    ) {}

    /**
     * Listado paginado con búsqueda por RIF/Cédula, nombre o correo.
     */
    public function paginate(
        ?string $search = null,
        ?string $type = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->repository->paginate($search, $type, $sortField, $sortDirection, $perPage);
    }

    /**
     * Obtiene todos los clientes activos sin paginar (para selectores de facturación y POS).
     *
     * @return Collection<int, Customer>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }
}