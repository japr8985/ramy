<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Domain\Models;

use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountsReceivableItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'accounts_receivable_items';

    protected $fillable = [
        'accounts_receivable_id',
        'product_id',
        'quantity',
        'cost_price',
        'unit_price',
        'discount',
        'final_price',
        'subtotal',
    ];

    protected $casts = [
        'id'                     => 'string',
        'accounts_receivable_id' => 'string',
        'product_id'             => 'string',
        'quantity'               => 'integer',
        'cost_price'             => 'decimal:4',
        'unit_price'             => 'decimal:4',
        'discount'               => 'decimal:4',
        'final_price'            => 'decimal:4',
        'subtotal'               => 'decimal:4',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    public function accountsReceivable(): BelongsTo
    {
        return $this->belongsTo(AccountsReceivable::class, 'accounts_receivable_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}