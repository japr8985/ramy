<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\CreateCategoryAction;
use App\Modules\Catalog\Application\Actions\DeleteCategoryAction;
use App\Modules\Catalog\Application\Actions\GetCategoriesAction;
use App\Modules\Catalog\Application\Actions\UpdateCategoryAction;
use App\Modules\Catalog\Application\DTOs\CreateCategoryDTO;
use App\Modules\Catalog\Application\DTOs\UpdateCategoryDTO;
use App\Modules\Catalog\Domain\Exceptions\CannotDeleteCategoryWithProductsException;
use App\Modules\Catalog\Domain\Repositories\CategoryRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Http\Requests\CreateCategoryRequest;
use App\Modules\Catalog\Infrastructure\Http\Requests\UpdateCategoryRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request, GetCategoriesAction $action): JsonResponse
    {
        // Si se pide para un selector/dropdown sin paginar
        if ($request->boolean('all')) {
            $categories = $action->all();

            return $this->success(
                data: $categories,
                message: 'Categorías obtenidas correctamente.'
            );
        }

        $search = $request->string('search')->toString() ?: null;
        $sortField = $request->string('sort_field', 'name')->toString();
        $sortDirection = $request->string('sort_direction', 'asc')->toString();
        $perPage = $request->integer('per_page', 10);

        $paginated = $action->paginate($search, $sortField, $sortDirection, $perPage);

        return $this->success(
            data: $paginated,
            message: 'Listado de categorías obtenido correctamente.'
        );
    }
    public function show(string $id, CategoryRepositoryInterface $repository): JsonResponse
{
    $category = $repository->findById($id);

    if (! $category) {
        return $this->error(
            message: 'La categoría solicitada no existe.',
            statusCode: 404
        );
    }

    return $this->success(
        data: $category,
        message: 'Categoría obtenida correctamente.'
    );
}

    public function store(CreateCategoryRequest $request, CreateCategoryAction $action): JsonResponse
    {
        $dto = CreateCategoryDTO::fromArray($request->validated());
        $category = $action->execute($dto);

        return $this->created(
            data: $category,
            message: 'Categoría creada con éxito.'
        );
    }

    public function update(
        UpdateCategoryRequest $request,
        string $id,
        UpdateCategoryAction $action
    ): JsonResponse {
        try {
            $dto = UpdateCategoryDTO::fromArray($id, $request->validated());
            $category = $action->execute($dto);

            return $this->success(
                data: $category,
                message: 'Categoría actualizada con éxito.'
            );
        } catch (ModelNotFoundException) {
            return $this->error(
                message: 'La categoría solicitada no existe.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }
    }

    public function destroy(string $id, DeleteCategoryAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return $this->success(
                data: null,
                message: 'Categoría eliminada del sistema correctamente.'
            );
        } catch (CannotDeleteCategoryWithProductsException $e) {
            return $this->error(
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        } catch (ModelNotFoundException) {
            return $this->error(
                message: 'La categoría solicitada no existe.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }
    }
}