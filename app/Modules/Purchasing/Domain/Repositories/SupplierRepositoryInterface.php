<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Repositories;

use App\Modules\Purchasing\Domain\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    public function all(): Collection;

    public function findById(string $id): ?Supplier;

    public function create(array $data, array $productIds = []): Supplier;

    public function update(Supplier $supplier, array $data, ?array $productIds = null): bool;

    public function delete(Supplier $supplier): bool;

    public function getStats(): array;
}