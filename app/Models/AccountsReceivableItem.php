<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable('accounts_receivable_id', 'product_id', 'quantity', 'cost_price', 'unit_price', 'discount', 'final_price', 'subtotal')]
class AccountsReceivableItem extends Model
{
    use HasUuids;
}