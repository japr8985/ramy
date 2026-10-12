<?php

declare(strict_types=1);

namespace App\Modules\Parties\Application\Actions;

use App\Modules\Parties\Application\DTOs\UpdateCustomerDTO;
use App\Modules\Parties\Domain\Models\Customer;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpdateCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
    ) {}

    public function execute(UpdateCustomerDTO $dto): Customer
    {
        $customer = $this->repository->findById($dto->id);

        if (! $customer) {
            throw new ModelNotFoundException("Cliente con ID {$dto->id} no encontrado.");
        }

        $this->repository->update($customer, $dto->toArray());

        return $customer->fresh();
    }
}