<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Models;

use App\Modules\Parties\Domain\Models\Customer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sales';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'created_by',
        'sale_date',
        'status',
        'subtotal',
        'total_discount',
        'total',
        'exchange_rate',
        'cash_received',
        'change',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'id'             => 'string',
        'invoice_number' => 'string',
        'customer_id'    => 'string',
        'created_by'     => 'string',
        'sale_date'      => 'datetime',
        'status'         => 'string',
        'subtotal'       => 'decimal:4',
        'total_discount' => 'decimal:4',
        'total'          => 'decimal:4',
        'exchange_rate'  => 'decimal:4',
        'cash_received'  => 'decimal:4',
        'change'         => 'decimal:4',
        'payment_method' => 'string',
        'notes'          => 'string',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function isCredit(): bool
    {
        return $this->payment_method === 'credit';
    }
}