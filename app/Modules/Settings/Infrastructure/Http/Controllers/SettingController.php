<?php

declare(strict_types=1);

namespace App\Modules\Settings\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Application\Actions\GetSettingsAction;
use App\Modules\Settings\Application\Actions\UpdateSettingsAction;
use App\Modules\Settings\Application\DTOs\UpdateSettingsDTO;
use App\Modules\Settings\Infrastructure\Http\Requests\UpdateSettingsRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class SettingController extends Controller
{
    use ApiResponse;

    public function index(GetSettingsAction $action): JsonResponse
    {
        $settings = $action->execute();

        return $this->success(
            data: $settings,
            message: 'Configuraciones obtenidas correctamente.'
        );
    }

    public function update(
        UpdateSettingsRequest $request,
        UpdateSettingsAction $action,
        GetSettingsAction $getSettingsAction
    ): JsonResponse {
        $dto = UpdateSettingsDTO::fromArray($request->validated());

        $action->execute($dto);

        // Devolvemos el estado actualizado listo para refrescar el frontend
        $updatedSettings = $getSettingsAction->execute();

        return $this->success(
            data: $updatedSettings,
            message: 'Configuraciones actualizadas correctamente.'
        );
    }
}