<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivablePayment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'receivable_payments';

    protected $fillable = [
        'accounts_receivable_id',
        'payment_date',
        'amount_usd',
        'exchange_rate',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'id'                     => 'string',
        'accounts_receivable_id' => 'string',
        'payment_date'           => 'datetime',
        'amount_usd'             => 'decimal:4',
        'exchange_rate'          => 'decimal:4',
        'payment_method'         => 'string',
        'notes'                  => 'string',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    public function accountsReceivable(): BelongsTo
    {
        return $this->belongsTo(AccountsReceivable::class, 'accounts_receivable_id');
    }
}