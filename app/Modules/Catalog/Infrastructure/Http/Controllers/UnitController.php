<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\CreateUnitAction;
use App\Modules\Catalog\Application\Actions\GetUnitsAction;
use App\Modules\Catalog\Application\DTOs\CreateUnitDTO;
use App\Modules\Catalog\Infrastructure\Http\Requests\CreateUnitRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class UnitController extends Controller
{
    use ApiResponse;

    public function index(GetUnitsAction $action): JsonResponse
    {
        $units = $action->execute();

        return $this->success(
            data: $units,
            message: 'Unidades de medida obtenidas correctamente.'
        );
    }

    public function store(CreateUnitRequest $request, CreateUnitAction $action): JsonResponse
    {
        $dto = CreateUnitDTO::fromArray($request->validated());

        $unit = $action->execute($dto);

        return $this->created(
            data: $unit,
            message: 'Unidad de medida creada correctamente.'
        );
    }
}