<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Purchasing\Application\Actions\DeleteSupplierAction;
use App\Modules\Purchasing\Application\Actions\GetSuppliersAction;
use App\Modules\Purchasing\Application\Actions\SaveSupplierAction;
use App\Modules\Purchasing\Application\DTOs\SaveSupplierDTO;
use App\Modules\Purchasing\Domain\Repositories\SupplierRepositoryInterface;
use App\Modules\Purchasing\Infrastructure\Http\Requests\SaveSupplierRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SupplierController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetSuppliersAction $action): JsonResponse
    {
        $search = $request->string('search')->toString() ?: null;
        $sortField = $request->string('sort_field', 'created_at')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $suppliers = $action->execute(
            search: $search,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: $perPage
        );

        return $this->success(
            data: $suppliers,
            message: 'Listado de proveedores obtenido correctamente.'
        );
    }

    public function all(SupplierRepositoryInterface $repository): JsonResponse
    {
        return $this->success(
            data: $repository->all(),
            message: 'Lista completa de proveedores obtenida correctamente.'
        );
    }

    public function stats(SupplierRepositoryInterface $repository): JsonResponse
    {
        return $this->success(
            data: $repository->getStats(),
            message: 'Estadísticas de proveedores obtenidas correctamente.'
        );
    }

    public function store(SaveSupplierRequest $request, SaveSupplierAction $action): JsonResponse
    {
        $dto = SaveSupplierDTO::fromArray($request->validated());
        $supplier = $action->execute($dto);

        return $this->success(
            data: $supplier,
            message: 'Proveedor registrado exitosamente en la cadena de suministro.',
            statusCode: 201
        );
    }

    public function show(string $id, SupplierRepositoryInterface $repository): JsonResponse
    {
        $supplier = $repository->findById($id);

        if (! $supplier) {
            return $this->error(message: 'Proveedor no encontrado.', statusCode: 404);
        }

        return $this->success(data: $supplier, message: 'Detalle de proveedor obtenido.');
    }

    public function update(SaveSupplierRequest $request, string $id, SaveSupplierAction $action): JsonResponse
    {
        try {
            $dto = SaveSupplierDTO::fromArray($request->validated());
            $supplier = $action->execute($dto, $id);

            return $this->success(
                data: $supplier,
                message: 'Proveedor actualizado con éxito.'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 404);
        }
    }

    public function destroy(string $id, DeleteSupplierAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return $this->success(
                data: null,
                message: 'Proveedor archivado satisfactoriamente.'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 404);
        }
    }
}