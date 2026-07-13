<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('category_id', 'unit_id', 'sku', 'name', 'purchase_price', 'selling_price', 'quantity', 'min_stock', 'is_active', 'description', 'notes')]
class Product extends Model
{
    use HasUuids, HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Obtener la tasa de cambio actual optimizada en caché para evitar queries repetitivos
     */
    public static function getExchangeRate(): float
    {
        return (float) \Cache::remember('bcv_rate', 3600, function () {
            return \App\Models\Setting::where('key', 'bcv_rate')->value('value') ?? 1.0;
        });
    }

    /**
     * Atributo virtual para el Precio de Compra en Bolívares
     */
    protected function purchasePriceVes(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->purchase_price * self::getExchangeRate(),
        );
    }

    /**
     * Atributo virtual para el Precio de Venta en Bolívares
     */
    protected function sellingPriceVes(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->selling_price * self::getExchangeRate(),
        );
    }

    public function suppliers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Supplier::class, 'product_supplier');
    }

    /**
     * Un producto puede estar presente en muchos renglones de ventas históricas
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }
}
