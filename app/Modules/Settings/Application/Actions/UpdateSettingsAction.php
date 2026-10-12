<?php

declare(strict_types=1);

namespace App\Modules\Settings\Application\Actions;

use App\Modules\Settings\Application\DTOs\UpdateSettingsDTO;
use App\Modules\Settings\Domain\Repositories\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final readonly class UpdateSettingsAction
{
    public function __construct(
        private SettingRepositoryInterface $repository,
    ) {}

    /**
     * Persiste los cambios de configuración e invalida la caché de la tasa.
     */
    public function execute(UpdateSettingsDTO $dto): void
    {
        $this->repository->setMany($dto->toDatabaseArray());

        // Invalidar la caché de la tasa para sincronización inmediata
        Cache::forget('bcv_rate');
    }
}