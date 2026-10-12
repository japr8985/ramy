<?php

declare(strict_types=1);

namespace App\Modules\Parties\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Parties\Application\Actions\CreateCustomerAction;
use App\Modules\Parties\Application\Actions\DeleteCustomerAction;
use App\Modules\Parties\Application\Actions\GetCustomersAction;
use App\Modules\Parties\Application\Actions\UpdateCustomerAction;
use App\Modules\Parties\Application\DTOs\CreateCustomerDTO;
use App\Modules\Parties\Application\DTOs\UpdateCustomerDTO;
use App\Modules\Parties\Domain\Exceptions\CannotDeleteCustomerWithSalesException;
use App\Modules\Parties\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Parties\Infrastructure\Http\Requests\CreateCustomerRequest;
use App\Modules\Parties\Infrastructure\Http\Requests\UpdateCustomerRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetCustomersAction $action): JsonResponse
    {
        // Selectores de clientes rápidos (ej. búsqueda directa en el POS)
        if ($request->boolean('all')) {
            $customers = $action->all();

            return $this->success(
                data: $customers,
                message: 'Clientes obtenidos correctamente.'
            );
        }

        $search = $request->string('search')->toString() ?: null;
        $type = $request->string('type')->toString() ?: null;
        $sortField = $request->string('sort_field', 'created_at')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $paginated = $action->paginate($search, $type, $sortField, $sortDirection, $perPage);

        return $this->success(
            data: $paginated,
            message: 'Listado de clientes obtenido correctamente.'
        );
    }

    public function show(string $id, CustomerRepositoryInterface $repository): JsonResponse
    {
        $customer = $repository->findById($id);

        if (! $customer) {
            return $this->error(
                message: 'El cliente solicitado no existe.',
                statusCode: 404
            );
        }

        return $this->success(
            data: $customer,
            message: 'Cliente obtenido correctamente.'
        );
    }

    public function store(CreateCustomerRequest $request, CreateCustomerAction $action): JsonResponse
    {
        $dto = CreateCustomerDTO::fromArray($request->validated());
        $customer = $action->execute($dto);

        return $this->success(
            data: $customer,
            message: 'Cliente registrado con éxito.',
            statusCode: 201
        );
    }

    public function update(
        UpdateCustomerRequest $request,
        string $id,
        UpdateCustomerAction $action
    ): JsonResponse {
        try {
            $dto = UpdateCustomerDTO::fromArray($id, $request->validated());
            $customer = $action->execute($dto);

            return $this->success(
                data: $customer,
                message: 'Cliente actualizado con éxito.'
            );
        } catch (ModelNotFoundException) {
            return $this->error(
                message: 'El cliente solicitado no existe.',
                statusCode: 404
            );
        }
    }

    public function destroy(string $id, DeleteCustomerAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return $this->success(
                data: null,
                message: 'Cliente eliminado correctamente.'
            );
        } catch (CannotDeleteCustomerWithSalesException $e) {
            return $this->error(
                message: $e->getMessage(),
                statusCode: 422
            );
        } catch (ModelNotFoundException) {
            return $this->error(
                message: 'El cliente solicitado no existe.',
                statusCode: 404
            );
        }
    }
}