<?php

declare(strict_types=1);

namespace App\Modules\Parties\Application\Actions;

use App\Modules\Parties\Application\DTOs\CreateCustomerDTO;
use App\Modules\Parties\Domain\Models\Customer;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;

final readonly class CreateCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
    ) {}

    public function execute(CreateCustomerDTO $dto): Customer
    {
        return $this->repository->create($dto->toArray());
    }
}