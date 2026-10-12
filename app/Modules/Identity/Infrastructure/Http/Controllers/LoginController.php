<?php

// namespace App\Modules\Identity\Infrastructure\Http\Controllers;
namespace App\Modules\Identity\Infrastructure\Http\Controllers;

use App\Modules\Identity\Application\Actions\AuthenticateUserAction;
use App\Modules\Identity\Application\Actions\LogoutUserAction;
use App\Modules\Identity\Application\DTOs\LoginDTO;
use App\Modules\Identity\Infrastructure\Http\Requests\LoginRequest;
use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController 
{
    use ApiResponse;
    public function login(LoginRequest $request, AuthenticateUserAction $action): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request->validated());
        $data = $action->execute($dto);

        return $this->success($data);
    }

    public function me(): JsonResponse
    {
        $me = auth('api')->user();
        return $this->success($me);
    }

    public function logout(Request $request, LogoutUserAction $action): JsonResponse
    {
        $action->execute($request->user());

        return $this->success(
            data: null,
            message: 'Sesión cerrada correctamente.'
        );
    }
}