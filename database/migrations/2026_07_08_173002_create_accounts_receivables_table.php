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
        Schema::create('accounts_receivables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sale_id')->constrained('sales')->onDelete('cascade'); // Vinculación directa a la venta
            $table->foreignUuid('customer_id')->constrained('customers')->restrictOnDelete();
            $table->dateTime('sale_date')->index();
            $table->dateTime('due_date')->index();
            
            // Todo lo financiero se congela en DÓLARES ($)
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->decimal('balance', 15, 4)->default(0); // Lo que aún debe en $
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_receivables');
    }
};
