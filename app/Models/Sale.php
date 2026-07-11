<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable('invoice_number', 'customer_id', 'created_by', 'sale_date', 'status', 'subtotal', 'total_discount', 'total', 'exchange_rate', 'cash_received', 'change', 'payment_method', 'notes')]
class Sale extends Model
{
    use HasUuids;
}
