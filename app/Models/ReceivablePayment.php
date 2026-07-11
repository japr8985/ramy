<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReceivablePayment extends Model
{
 
    use HasUuids;
    protected $fillable = ['accounts_receivable_id', 'payment_date', 'amount_usd', 'exchange_rate', 'payment_method', 'notes'];

}
