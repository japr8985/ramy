<?php

declare(strict_types=1);

namespace App\Modules\Parties\Application\Actions;

use App\Modules\Parties\Domain\Exceptions\CannotDeleteCustomerWithSalesException;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeleteCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
    ) {}

    /**
     * @throws CannotDeleteCustomerWithSalesException
     * @throws ModelNotFoundException
     */
    public function execute(string $id): void
    {
        $customer = $this->repository->findByIdWithSalesCount($id);

        if (! $customer) {
            throw new ModelNotFoundException("Cliente con ID {$id} no encontrado.");
        }

        $salesCount = (int) ($customer->sales_count ?? 0);

        if ($salesCount > 0) {
            throw CannotDeleteCustomerWithSalesException::hasHistoricalSales($salesCount);
        }

        $this->repository->delete($customer);
    }
}