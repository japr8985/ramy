<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Models;

use App\Modules\Sales\Domain\Models\SaleItem;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'unit_id',
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'quantity',
        'min_stock',
        'is_active',
        'description',
        'notes',
    ];

    protected $casts = [
        'id'             => 'string',
        'category_id'    => 'string',
        'unit_id'        => 'string',
        'sku'            => 'string',
        'name'           => 'string',
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'quantity'       => 'integer',
        'min_stock'      => 'integer',
        'is_active'      => 'boolean',
        'description'    => 'string',
        'notes'          => 'string',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relación con los renglones de ventas históricas.
     * (Apunta a la tabla puente/renglones cuando abordemos el módulo Sales).
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }

    /**
     * Comprueba si el producto se encuentra en nivel crítico de inventario.
     */
    public function isStockCritical(): bool
    {
        return $this->quantity < $this->min_stock;
    }

    /**
     * Calcula el precio de venta en moneda nacional dada una tasa de cambio específica.
     */
    public function calculateSellingPriceVes(float $exchangeRate): float
    {
        return round((float) $this->selling_price * $exchangeRate, 2);
    }

    /**
     * Calcula el precio de costo en moneda nacional dada una tasa de cambio específica.
     */
    public function calculatePurchasePriceVes(float $exchangeRate): float
    {
        return round((float) $this->purchase_price * $exchangeRate, 2);
    }
}