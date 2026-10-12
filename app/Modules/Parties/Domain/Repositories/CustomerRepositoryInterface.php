<?php

declare(strict_types=1);

namespace App\Modules\Parties\Domain\Repositories;

use App\Modules\Parties\Domain\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    /**
     * Obtiene el listado paginado con soporte de búsqueda en name, rif_ci y email.
     */
    public function paginate(
        ?string $search = null,
        ?string $type = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    /**
     * Obtiene todos los clientes sin paginación (para selects rápidos del POS).
     *
     * @return Collection<int, Customer>
     */
    public function all(): Collection;

    public function findById(string $id): ?Customer;

    public function findByIdWithSalesCount(string $id): ?Customer;

    public function findByRifCi(string $rifCi): ?Customer;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Customer;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Customer $customer, array $data): bool;

    public function delete(Customer $customer): bool;
}