<?php

declare(strict_types=1);

namespace App\Modules\Sales\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sales\Application\Actions\GetSalesAction;
use App\Modules\Sales\Application\Actions\ProcessSaleAction;
use App\Modules\Sales\Application\DTOs\ProcessSaleDTO;
use App\Modules\Sales\Domain\Exceptions\CustomerRequiredForCreditSaleException;
use App\Modules\Sales\Domain\Exceptions\EmptySaleCartException;
use App\Modules\Sales\Domain\Exceptions\InsufficientProductStockException;
use App\Modules\Sales\Domain\Repositories\SaleRepositoryInterface;
use App\Modules\Sales\Infrastructure\Http\Requests\ProcessSaleRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SaleController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetSalesAction $action): JsonResponse
    {
        $search = $request->string('search')->toString() ?: null;
        $status = $request->string('status')->toString() ?: null;
        $paymentMethod = $request->string('payment_method')->toString() ?: null;
        $sortField = $request->string('sort_field', 'sale_date')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $sales = $action->execute($search, $status, $paymentMethod, $sortField, $sortDirection, $perPage);

        return $this->success(
            data: $sales,
            message: 'Historial de ventas obtenido correctamente.'
        );
    }

    public function show(string $id, SaleRepositoryInterface $repository): JsonResponse
    {
        $sale = $repository->findById($id);

        if (! $sale) {
            return $this->error(
                message: 'La venta solicitada no existe.',
                statusCode: 404
            );
        }

        return $this->success(
            data: $sale,
            message: 'Detalle de la venta obtenido correctamente.'
        );
    }

    public function store(ProcessSaleRequest $request, ProcessSaleAction $action): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = $request->user()?->id;

            $dto = ProcessSaleDTO::fromArray($validated);
            $sale = $action->execute($dto);

            return $this->success(
                data: $sale,
                message: 'Venta procesada e inventario actualizado con éxito.',
                statusCode: 201
            );
        } catch (EmptySaleCartException|CustomerRequiredForCreditSaleException|InsufficientProductStockException $e) {
            return $this->error(
                message: $e->getMessage(),
                statusCode: 422
            );
        }
    }
}