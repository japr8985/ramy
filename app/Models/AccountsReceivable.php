<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable('sale_id', 'customer_id', 'sale_date', 'due_date', 'total', 'paid_amount', 'balance')]
class AccountsReceivable extends Model
{
    use HasUuids;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReceivablePayment::class, 'accounts_receivable_id');
    }

    // Atributo virtual dinámico para saber cuánto debe en Bolívares HOY
    protected function balanceVes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->balance * Product::getExchangeRate(),
        );
    }
}
