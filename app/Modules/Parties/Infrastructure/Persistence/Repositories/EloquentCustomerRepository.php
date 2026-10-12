<?php

declare(strict_types=1);

namespace App\Modules\Parties\Infrastructure\Persistence\Repositories;

use App\Modules\Parties\Domain\Models\Customer;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(
        ?string $search = null,
        ?string $type = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        $allowedSorts = ['name', 'rif_ci', 'email', 'created_at'];
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $column = in_array($sortField, $allowedSorts, true) ? $sortField : 'created_at';

        return Customer::query()
            ->withCount('sales')
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'ilike', "%{$term}%")
                      ->orWhere('rif_ci', 'ilike', "%{$term}%")
                      ->orWhere('email', 'ilike', "%{$term}%");
                });
            })
            ->orderBy($column, $direction)
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Customer::query()
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findById(string $id): ?Customer
    {
        return Customer::query()->find($id);
    }

    public function findByIdWithSalesCount(string $id): ?Customer
    {
        return Customer::query()
            ->withCount('sales')
            ->find($id);
    }

    public function findByRifCi(string $rifCi): ?Customer
    {
        return Customer::query()
            ->where('rif_ci', $rifCi)
            ->first();
    }

    public function create(array $data): Customer
    {
        return Customer::query()->create($data);
    }

    public function update(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }
}