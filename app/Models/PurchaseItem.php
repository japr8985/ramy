<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable('purchase_id', 'product_id', 'quantity', 'unit_price', 'selling_price', 'subtotal')]
class PurchaseItem extends Model
{
    use HasUuids;
}
