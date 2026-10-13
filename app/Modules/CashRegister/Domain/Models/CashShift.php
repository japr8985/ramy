<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Models;

use App\Models\User;
use App\Modules\Sales\Domain\Models\Sale;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashShift extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cash_shifts';

    protected $fillable = [
        'user_id',
        'status',
        'opened_at',
        'closed_at',
        'initial_cash_usd',
        'initial_cash_ves',
        'expected_cash_usd',
        'expected_cash_ves',
        'actual_cash_usd',
        'actual_cash_ves',
        'difference_usd',
        'difference_ves',
        'notes',
    ];

    protected $casts = [
        'id'                => 'string',
        'user_id'           => 'string',
        'status'            => 'string',
        'opened_at'         => 'datetime',
        'closed_at'         => 'datetime',
        'initial_cash_usd'  => 'decimal:4',
        'initial_cash_ves'  => 'decimal:4',
        'expected_cash_usd' => 'decimal:4',
        'expected_cash_ves' => 'decimal:4',
        'actual_cash_usd'   => 'decimal:4',
        'actual_cash_ves'   => 'decimal:4',
        'difference_usd'    => 'decimal:4',
        'difference_ves'    => 'decimal:4',
        'notes'             => 'string',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'cash_shift_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cash_shift_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}