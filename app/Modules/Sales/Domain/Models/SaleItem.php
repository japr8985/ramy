<?php

declare(strict_types=1);

namespace App\Modules\Sales\Domain\Models;

use App\Modules\Catalog\Domain\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'id'         => 'string',
        'sale_id'    => 'string',
        'product_id' => 'string',
        'quantity'   => 'integer',
        'price'      => 'decimal:4',
        'subtotal'   => 'decimal:4',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}