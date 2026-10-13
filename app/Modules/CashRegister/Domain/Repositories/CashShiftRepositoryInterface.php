<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Repositories;

use App\Modules\CashRegister\Domain\Models\CashMovement;
use App\Modules\CashRegister\Domain\Models\CashShift;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CashShiftRepositoryInterface
{
    /**
     * Obtiene el listado histórico paginado de turnos de caja.
     */
    public function paginate(
        ?string $userId = null,
        ?string $status = null,
        string $sortField = 'opened_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findById(string $id): ?CashShift;

    /**
     * Obtiene el turno activo ('open') asignado a un usuario específico.
     */
    public function findOpenShiftByUserId(string $userId): ?CashShift;

    /**
     * @param array<string, mixed> $data
     */
    public function createShift(array $data): CashShift;

    /**
     * @param array<string, mixed> $data
     */
    public function updateShift(CashShift $shift, array $data): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function createMovement(array $data): CashMovement;

    /**
     * Calcula los totales acumulados de ventas en efectivo y movimientos dentro de un turno.
     *
     * @return array{
     *     cash_sales_usd: float,
     *     cash_sales_ves: float,
     *     manual_in_usd: float,
     *     manual_in_ves: float,
     *     manual_out_usd: float,
     *     manual_out_ves: float,
     *     expected_usd: float,
     *     expected_ves: float
     * }
     */
    public function calculateShiftTotals(CashShift $shift): array;
}