<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Domain\Models;

use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'purchase_items';

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'selling_price',
        'subtotal',
    ];

    protected $casts = [
        'id'            => 'string',
        'purchase_id'   => 'string',
        'product_id'    => 'string',
        'quantity'      => 'integer',
        'unit_price'    => 'decimal:4',
        'selling_price' => 'decimal:4',
        'subtotal'      => 'decimal:4',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}