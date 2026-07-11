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
        Schema::create('receivable_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('accounts_receivable_id')->constrained('accounts_receivables')->onDelete('cascade');
            $table->dateTime('payment_date')->index();
            
            // Dinámica Cambiaria de Venezuela
            $table->decimal('amount_usd', 15, 4);      // Cuánto abonó equivalente en $
            $table->decimal('exchange_rate', 10, 4);   // Tasa BCV al momento de este abono
            
            $table->string('payment_method');          // Transferencia, Efectivo, Pago Móvil
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
    }
};
