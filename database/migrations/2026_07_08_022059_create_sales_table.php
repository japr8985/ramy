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
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('sale_date')->index();
            $table->string('status')->index();
            
            // Totales ampliados a 15,4 para evitar desbordamientos
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('total_discount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);

            // Registro histórico de la tasa del día de la venta
            $table->decimal('exchange_rate', 10, 4)->default(1); 

            $table->decimal('cash_received', 15, 4)->default(0);
            $table->decimal('change', 15, 4)->default(0);
            $table->string('payment_method')->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
