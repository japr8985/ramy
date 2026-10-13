<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Turnos / Sesiones de Caja
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            
            // Estado y marcas temporales
            $table->string('status', 20)->default('open')->index(); // 'open', 'closed'
            $table->dateTime('opened_at')->index();
            $table->dateTime('closed_at')->nullable();

            // Montos iniciales de apertura (Fondo de caja / Base)
            $table->decimal('initial_cash_usd', 15, 4)->default(0);
            $table->decimal('initial_cash_ves', 15, 4)->default(0);

            // Montos esperados por sistema al momento del arqueo/cierre
            // (Base + Ventas en efectivo + Entradas manuales - Salidas manuales)
            $table->decimal('expected_cash_usd', 15, 4)->default(0);
            $table->decimal('expected_cash_ves', 15, 4)->default(0);

            // Montos reales declarados/contados por el cajero
            $table->decimal('actual_cash_usd', 15, 4)->nullable();
            $table->decimal('actual_cash_ves', 15, 4)->nullable();

            // Diferencia resultante (actual - expected): positivo = sobrante, negativo = faltante
            $table->decimal('difference_usd', 15, 4)->default(0);
            $table->decimal('difference_ves', 15, 4)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
        // 2. Movimientos manuales de efectivo en caja (ingresos extraordinarios y retiros/gastos)
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cash_shift_id')->constrained('cash_shifts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();

            $table->string('type', 20)->index(); // 'in' (ingreso), 'out' (egreso / retiro)
            $table->string('currency', 10)->default('USD'); // 'USD', 'VES'
            $table->decimal('amount', 15, 4);
            $table->string('concept', 255); // Ej. 'Pago de flete menor', 'Aporte sencillo para cambio'
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // 3. Vincular la venta al turno de caja si existe uno abierto
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignUuid('cash_shift_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('cash_shifts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cash_shift_id']);
            $table->dropColumn('cash_shift_id');
        });

        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cash_shifts');
    }
};
