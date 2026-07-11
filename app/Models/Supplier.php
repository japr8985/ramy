<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('name', 'contact_person', 'email', 'phone', 'address', 'notes')]
class Supplier extends Model
{
    use HasUuids, SoftDeletes;
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier');
    }

    /**
     * Helper estético para las iniciales del Proveedor
     */
    public function getInitialsAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }
}
