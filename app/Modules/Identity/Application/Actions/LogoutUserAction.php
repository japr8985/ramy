<?php

namespace App\Modules\Identity\Application\Actions;

use App\Modules\Identity\Domain\Models\User;

final readonly class LogoutUserAction
{
    public function execute(User $user): void
    {
        // Revoca únicamente el token que se usó para la petición actual
        $user->currentAccessToken()->delete();

        // O si quisieras revocar todos los dispositivos a la vez:
        // $user->tokens()->delete();
    }
}