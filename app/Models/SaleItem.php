<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable('sale_id', 'product_id', 'quantity', 'price', 'subtotal')]
class SaleItem extends Model
{
    use HasUuids;
}
