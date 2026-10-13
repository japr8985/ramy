<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Models;

use App\Modules\Parties\Domain\Models\Customer;
use App\Modules\Sales\Domain\Models\Sale;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountsReceivable extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'accounts_receivables';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'sale_date',
        'due_date',
        'total',
        'paid_amount',
        'balance',
    ];

    protected $casts = [
        'id'          => 'string',
        'sale_id'     => 'string',
        'customer_id' => 'string',
        'sale_date'   => 'datetime',
        'due_date'    => 'datetime',
        'total'       => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'balance'     => 'decimal:4',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AccountsReceivableItem::class, 'accounts_receivable_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReceivablePayment::class, 'accounts_receivable_id');
    }

    public function isSettled(): bool
    {
        return (float) $this->balance <= 0.0;
    }

    public function isOverdue(): bool
    {
        return ! $this->isSettled() && $this->due_date->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->isSettled()) {
            return 'paid';
        }

        if ((float) $this->paid_amount > 0) {
            return 'partial';
        }

        return 'pending';
    }
}