<?php

declare(strict_types=1);

namespace App\Modules\Settings\Domain\Repositories;

interface SettingRepositoryInterface
{
    /**
     * Obtiene el valor de una configuración por su clave.
     */
    public function get(string $key, ?string $default = null): ?string;

    /**
     * Obtiene todas las configuraciones mapeadas como un arreglo asociativo [key => value].
     *
     * @return array<string, string|null>
     */
    public function getAllAsKeyValue(): array;

    /**
     * Establece o actualiza múltiples configuraciones de forma atómica.
     *
     * @param array<string, string|null> $settings
     */
    public function setMany(array $settings): void;

    /**
     * Elimina una configuración por clave.
     */
    public function forget(string $key): void;
}