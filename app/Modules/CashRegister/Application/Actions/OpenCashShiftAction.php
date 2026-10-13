<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Application\Actions;

use App\Modules\CashRegister\Application\DTOs\OpenCashShiftDTO;
use App\Modules\CashRegister\Domain\Exceptions\UserAlreadyHasOpenShiftException;
use App\Modules\CashRegister\Domain\Models\CashShift;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;

final readonly class OpenCashShiftAction
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
    ) {}

    /**
     * @throws UserAlreadyHasOpenShiftException
     */
    public function execute(OpenCashShiftDTO $dto): CashShift
    {
        $existing = $this->repository->findOpenShiftByUserId($dto->userId);

        if ($existing) {
            throw UserAlreadyHasOpenShiftException::forUser($dto->userId);
        }

        return $this->repository->createShift($dto->toArray());
    }
}