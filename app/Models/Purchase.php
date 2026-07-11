<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
#[Fillable('id','invoice_number','supplier_id','purchase_date','due_date','total','status','notes','proof_image','created_by')]
class Purchase extends Model
{
    use HasUuids;
}
            