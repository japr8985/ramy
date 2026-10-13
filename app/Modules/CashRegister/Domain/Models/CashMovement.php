<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cash_movements';

    protected $fillable = [
        'cash_shift_id',
        'user_id',
        'type',
        'currency',
        'amount',
        'concept',
        'notes',
    ];

    protected $casts = [
        'id'            => 'string',
        'cash_shift_id' => 'string',
        'user_id'       => 'string',
        'type'          => 'string',
        'currency'      => 'string',
        'amount'        => 'decimal:4',
        'concept'       => 'string',
        'notes'         => 'string',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'cash_shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isIncome(): bool
    {
        return $this->type === 'in';
    }

    public function isExpense(): bool
    {
        return $this->type === 'out';
    }
}