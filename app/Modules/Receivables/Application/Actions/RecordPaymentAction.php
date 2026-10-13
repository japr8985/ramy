<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Application\Actions;

use App\Modules\Receivables\Application\DTOs\RecordPaymentDTO;
use App\Modules\Receivables\Domain\Exceptions\InvalidPaymentAmountException;
use App\Modules\Receivables\Domain\Exceptions\PaymentExceedsBalanceException;
use App\Modules\Receivables\Domain\Exceptions\ReceivableAlreadySettledException;
use App\Modules\Receivables\Domain\Models\ReceivablePayment;
use App\Modules\Receivables\Domain\Repositories\AccountsReceivableRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class RecordPaymentAction
{
    public function __construct(
        private AccountsReceivableRepositoryInterface $repository,
    ) {}

    /**
     * @throws ModelNotFoundException
     * @throws InvalidPaymentAmountException
     * @throws ReceivableAlreadySettledException
     * @throws PaymentExceedsBalanceException
     */
    public function execute(RecordPaymentDTO $dto): ReceivablePayment
    {
        // 1. Validar monto positivo
        if ($dto->amountUsd <= 0.0) {
            throw InvalidPaymentAmountException::nonPositive();
        }

        // 2. Localizar la cuenta por cobrar
        $receivable = $this->repository->findById($dto->receivableId);

        if (! $receivable) {
            throw new ModelNotFoundException("Cuenta por cobrar con ID '{$dto->receivableId}' no encontrada.");
        }

        // 3. Validar si ya está completamente pagada
        if ($receivable->isSettled()) {
            throw ReceivableAlreadySettledException::forId($receivable->id);
        }

        // 4. Validar que el abono no sobrepase el saldo deudor
        $currentBalance = (float) $receivable->balance;
        if (round($dto->amountUsd, 4) > round($currentBalance, 4)) {
            throw PaymentExceedsBalanceException::withAmounts($dto->amountUsd, $currentBalance);
        }

        // 5. Aplicar la transacción atómica mediante el repositorio
        return $this->repository->applyPayment(
            receivable: $receivable,
            paymentData: $dto->toPaymentArray(),
            amountUsd: $dto->amountUsd
        );
    }
}