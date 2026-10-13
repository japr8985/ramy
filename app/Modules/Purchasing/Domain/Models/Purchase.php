<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'purchases';

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'purchase_date',
        'due_date',
        'total',
        'status',
        'notes',
        'proof_image',
        'created_by',
    ];

    protected $casts = [
        'id'             => 'string',
        'invoice_number' => 'string',
        'supplier_id'    => 'string',
        'purchase_date'  => 'date',
        'due_date'       => 'date',
        'total'          => 'decimal:2',
        'status'         => 'string',
        'notes'          => 'string',
        'proof_image'    => 'string',
        'created_by'     => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }
}