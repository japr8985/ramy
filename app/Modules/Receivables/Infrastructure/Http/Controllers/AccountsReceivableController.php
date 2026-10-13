<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Receivables\Application\Actions\GetReceivableByIdAction;
use App\Modules\Receivables\Application\Actions\GetReceivablesAction;
use App\Modules\Receivables\Application\Actions\GetReceivablesStatsAction;
use App\Modules\Receivables\Application\Actions\RecordPaymentAction;
use App\Modules\Receivables\Application\DTOs\RecordPaymentDTO;
use App\Modules\Receivables\Domain\Exceptions\InvalidPaymentAmountException;
use App\Modules\Receivables\Domain\Exceptions\PaymentExceedsBalanceException;
use App\Modules\Receivables\Domain\Exceptions\ReceivableAlreadySettledException;
use App\Modules\Receivables\Infrastructure\Http\Requests\RecordPaymentRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountsReceivableController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetReceivablesAction $action): JsonResponse
    {
        $search = $request->string('search')->toString() ?: null;
        $customerId = $request->string('customer_id')->toString() ?: null;
        $status = $request->string('status')->toString() ?: null;
        $onlyWithBalance = $request->boolean('only_with_balance', true);
        $sortField = $request->string('sort_field', 'due_date')->toString();
        $sortDirection = $request->string('sort_direction', 'asc')->toString();
        $perPage = $request->integer('per_page', 10);

        $receivables = $action->execute(
            search: $search,
            customerId: $customerId,
            status: $status,
            onlyWithBalance: $onlyWithBalance,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

        return $this->success(
            data: $receivables,
            message: 'Listado de cuentas por cobrar obtenido correctamente.'
        );
    }

    public function stats(GetReceivablesStatsAction $action): JsonResponse
    {
        $stats = $action->execute();

        return $this->success(
            data: $stats->toArray(),
            message: 'Métricas de cuentas por cobrar obtenidas correctamente.'
        );
    }

    public function show(string $id, GetReceivableByIdAction $action): JsonResponse
    {
        $receivable = $action->execute($id);

        if (! $receivable) {
            return $this->error(
                message: 'La cuenta por cobrar solicitada no existe.',
                statusCode: 404
            );
        }

        return $this->success(
            data: $receivable,
            message: 'Detalle de la cuenta por cobrar obtenido correctamente.'
        );
    }

    public function recordPayment(
        RecordPaymentRequest $request,
        string $id,
        RecordPaymentAction $action
    ): JsonResponse {
        try {
            $dto = RecordPaymentDTO::fromArray($id, $request->validated());
            $payment = $action->execute($dto);

            return $this->success(
                data: $payment,
                message: 'Abono procesado y saldo actualizado exitosamente.',
                statusCode: 201
            );
        } catch (ModelNotFoundException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 404);
        } catch (InvalidPaymentAmountException|PaymentExceedsBalanceException|ReceivableAlreadySettledException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 422);
        }
    }
}