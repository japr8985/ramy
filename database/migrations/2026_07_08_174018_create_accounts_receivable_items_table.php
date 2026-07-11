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
        Schema::create('accounts_receivable_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('accounts_receivable_id')->constrained('accounts_receivables')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('cost_price', 15, 4); // Para calcular la ganancia real neta después
            $table->decimal('unit_price', 15, 4); 
            $table->decimal('discount', 15, 4)->default(0); 
            $table->decimal('final_price', 15, 4); 
            $table->decimal('subtotal', 15, 4); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_receivable_items');
    }
};
