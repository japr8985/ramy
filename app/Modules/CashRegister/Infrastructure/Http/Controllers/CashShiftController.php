<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CashRegister\Application\Actions\CloseCashShiftAction;
use App\Modules\CashRegister\Application\Actions\GetCurrentShiftAction;
use App\Modules\CashRegister\Application\Actions\GetShiftSummaryAction;
use App\Modules\CashRegister\Application\Actions\OpenCashShiftAction;
use App\Modules\CashRegister\Application\Actions\RecordCashMovementAction;
use App\Modules\CashRegister\Application\DTOs\CloseCashShiftDTO;
use App\Modules\CashRegister\Application\DTOs\OpenCashShiftDTO;
use App\Modules\CashRegister\Application\DTOs\RecordCashMovementDTO;
use App\Modules\CashRegister\Domain\Exceptions\InvalidCashMovementAmountException;
use App\Modules\CashRegister\Domain\Exceptions\NoActiveShiftFoundException;
use App\Modules\CashRegister\Domain\Exceptions\ShiftAlreadyClosedException;
use App\Modules\CashRegister\Domain\Exceptions\UserAlreadyHasOpenShiftException;
use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;
use App\Modules\CashRegister\Infrastructure\Http\Requests\CloseCashShiftRequest;
use App\Modules\CashRegister\Infrastructure\Http\Requests\OpenCashShiftRequest;
use App\Modules\CashRegister\Infrastructure\Http\Requests\RecordCashMovementRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CashShiftController extends Controller
{
    use ApiResponse;

    /**
     * Listado histórico de turnos cerrados y abiertos.
     */
    public function index(Request $request, CashShiftRepositoryInterface $repository): JsonResponse
    {
        $userId = $request->string('user_id')->toString() ?: null;
        $status = $request->string('status')->toString() ?: null;
        $sortField = $request->string('sort_field', 'opened_at')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $shifts = $repository->paginate($userId, $status, $sortField, $sortDirection, $perPage);

        return $this->success(
            data: $shifts,
            message: 'Historial de turnos de caja obtenido correctamente.'
        );
    }

    /**
     * Devuelve el turno actualmente abierto para el cajero autenticado.
     */
    public function current(Request $request, GetCurrentShiftAction $action): JsonResponse
    {
        $userId = (string) $request->user()->id;
        $current = $action->execute($userId);

        if (! $current) {
            return $this->success(
                data: null,
                message: 'No hay ningún turno de caja abierto actualmente.'
            );
        }

        return $this->success(
            data: $current,
            message: 'Turno activo obtenido correctamente.'
        );
    }

    /**
     * Abre un nuevo turno de caja.
     */
    public function open(OpenCashShiftRequest $request, OpenCashShiftAction $action): JsonResponse
    {
        try {
            $userId = (string) $request->user()->id;
            $dto = OpenCashShiftDTO::fromArray($userId, $request->validated());
            $shift = $action->execute($dto);

            return $this->success(
                data: $shift,
                message: 'Turno de caja abierto correctamente.',
                statusCode: 201
            );
        } catch (UserAlreadyHasOpenShiftException $e) {
            return $this->error(
                message: $e->getMessage(),
                statusCode: 422
            );
        }
    }

    /**
     * Arqueo en vivo de un turno de caja específico.
     */
    public function summary(string $id, GetShiftSummaryAction $action): JsonResponse
    {
        try {
            $data = $action->execute($id);

            return $this->success(
                data: $data,
                message: 'Resumen y arqueo del turno obtenido correctamente.'
            );
        } catch (NoActiveShiftFoundException $e) {
            return $this->error(
                message: $e->getMessage(),
                statusCode: 404
            );
        }
    }

    /**
     * Registra un ingreso o egreso manual en el turno.
     */
    public function recordMovement(
        RecordCashMovementRequest $request,
        string $id,
        RecordCashMovementAction $action
    ): JsonResponse {
        try {
            $userId = (string) $request->user()->id;
            $dto = RecordCashMovementDTO::fromArray($id, $userId, $request->validated());
            $movement = $action->execute($dto);

            return $this->success(
                data: $movement,
                message: 'Movimiento de caja registrado correctamente.',
                statusCode: 201
            );
        } catch (NoActiveShiftFoundException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 404);
        } catch (ShiftAlreadyClosedException|InvalidCashMovementAmountException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 422);
        }
    }

    /**
     * Cierra el turno de caja y registra el arqueo final.
     */
    public function close(
        CloseCashShiftRequest $request,
        string $id,
        CloseCashShiftAction $action
    ): JsonResponse {
        try {
            $dto = CloseCashShiftDTO::fromArray($id, $request->validated());
            $shift = $action->execute($dto);

            return $this->success(
                data: $shift,
                message: 'Turno de caja cerrado y arqueo registrado con éxito.'
            );
        } catch (NoActiveShiftFoundException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 404);
        } catch (ShiftAlreadyClosedException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 422);
        }
    }
}