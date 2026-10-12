<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\CreateProductAction;
use App\Modules\Catalog\Application\Actions\DeleteProductAction;
use App\Modules\Catalog\Application\Actions\GetProductKpisAction;
use App\Modules\Catalog\Application\Actions\GetProductsAction;
use App\Modules\Catalog\Application\Actions\LookupBarcodeAction;
use App\Modules\Catalog\Application\Actions\UpdateProductAction;
use App\Modules\Catalog\Application\DTOs\CreateProductDTO;
use App\Modules\Catalog\Application\DTOs\UpdateProductDTO;
use App\Modules\Catalog\Domain\Exceptions\CannotDeleteProductWithSalesException;
use App\Modules\Catalog\Domain\Repositories\ProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Http\Requests\CreateProductRequest;
use App\Modules\Catalog\Infrastructure\Http\Requests\UpdateProductRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetProductsAction $action): JsonResponse
    {
        $search = $request->string('search')->toString() ?: null;
        $categoryId = $request->string('category_id')->toString() ?: null;
        $sortField = $request->string('sort_field', 'created_at')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();
        $perPage = $request->integer('per_page', 10);

        $products = $action->execute($search, $categoryId, $sortField, $sortDirection, $perPage);

        return $this->success(
            data: $products,
            message: 'Listado de productos obtenido correctamente.'
        );
    }

    public function kpis(GetProductKpisAction $action): JsonResponse
    {
        $kpis = $action->execute();

        return $this->success(
            data: $kpis,
            message: 'Indicadores de inventario calculados con éxito.'
        );
    }

    public function show(string $id, ProductRepositoryInterface $repository): JsonResponse
    {
        $product = $repository->findById($id);

        if (! $product) {
            return $this->error(message: 'Producto no encontrado.', statusCode: 404);
        }

        return $this->success(
            data: $product,
            message: 'Producto obtenido correctamente.'
        );
    }

    public function store(CreateProductRequest $request, CreateProductAction $action): JsonResponse
    {
        $dto = CreateProductDTO::fromArray($request->validated());
        $product = $action->execute($dto);

        return $this->success(
            data: $product,
            message: 'Producto registrado en el inventario con éxito.',
            statusCode: 201
        );
    }

    public function update(
        UpdateProductRequest $request,
        string $id,
        UpdateProductAction $action
    ): JsonResponse {
        try {
            $dto = UpdateProductDTO::fromArray($id, $request->validated());
            $product = $action->execute($dto);

            return $this->success(
                data: $product,
                message: 'Producto actualizado con éxito.'
            );
        } catch (ModelNotFoundException) {
            return $this->error(message: 'Producto no encontrado.', statusCode: 404);
        }
    }

    public function destroy(string $id, DeleteProductAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return $this->success(
                data: null,
                message: 'Producto removido del inventario con éxito.'
            );
        } catch (CannotDeleteProductWithSalesException $e) {
            return $this->error(message: $e->getMessage(), statusCode: 422);
        } catch (ModelNotFoundException) {
            return $this->error(message: 'Producto no encontrado.', statusCode: 404);
        }
    }

    public function lookupBarcode(string $barcode, LookupBarcodeAction $action): JsonResponse
    {
        $result = $action->execute($barcode);

        return $this->success(
            data: $result,
            message: 'Resolución de código de barras completada.'
        );
    }
}