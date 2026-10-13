<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Purchasing\Application\Actions\GetPurchaseByIdAction;
use App\Modules\Purchasing\Application\Actions\GetPurchasesAction;
use App\Modules\Purchasing\Application\Actions\ReceivePurchaseAction;
use App\Modules\Purchasing\Application\DTOs\ProcessPurchaseDTO;
use App\Modules\Purchasing\Domain\Exceptions\EmptyPurchaseCartException;
use App\Modules\Purchasing\Infrastructure\Http\Requests\ProcessPurchaseRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PurchaseController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetPurchasesAction $action): JsonResponse
    {
        $search = $request->string('search')->toString() ?: null;
        $supplierId = $request->string('supplier_id')->toString() ?: null;
        $status = $request->string('status')->toString() ?: null;
        $sortField = $request->string('sort_field', 'purchase_date')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $purchases = $action->execute(
            search: $search,
            supplierId: $supplierId,
            status: $status,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

        return $this->success(
            data: $purchases,
            message: 'Listado de compras obtenido con éxito.'
        );
    }

    public function show(string $id, GetPurchaseByIdAction $action): JsonResponse
    {
        $purchase = $action->execute($id);

        if (! $purchase) {
            return $this->error(message: 'La orden de compra solicitada no existe.', statusCode: 404);
        }

        return $this->success(
            data: $purchase,
            message: 'Detalle de la compra obtenido correctamente.'
        );
    }

    public function store(ProcessPurchaseRequest $request, ReceivePurchaseAction $action): JsonResponse
    {
        try {
            $userId = (string) $request->user()->id;
            $dto = ProcessPurchaseDTO::fromArray($request->validated(), $userId);
            $purchase = $action->execute($dto);

            return $this->success(
                data: $purchase,
                message: 'Compra registrada y existencias añadidas al inventario.',
                statusCode: 201
            );
        } catch (EmptyPurchaseCartException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 422);
        }
    }
}