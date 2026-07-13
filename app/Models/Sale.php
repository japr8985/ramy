<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable('invoice_number', 'customer_id', 'created_by', 'sale_date', 'status', 'subtotal', 'total_discount', 'total', 'exchange_rate', 'cash_received', 'change', 'payment_method', 'notes')]
class Sale extends Model
{
    use HasUuids;

   public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    /**
     * Una venta pertenece a un cliente (nullable)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
