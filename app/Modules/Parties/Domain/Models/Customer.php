<?php

declare(strict_types=1);

namespace App\Modules\Parties\Domain\Models;

use App\Modules\Sales\Domain\Models\Sale;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'rif_ci',
        'name',
        'email',
        'phone',
        'address',
        'type',
        'notes',
    ];

    protected $casts = [
        'id'         => 'string',
        'rif_ci'     => 'string',
        'name'       => 'string',
        'email'      => 'string',
        'phone'      => 'string',
        'address'    => 'string',
        'type'       => 'string',
        'notes'      => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación con ventas históricas.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    /**
     * Helper para obtener las iniciales de un cliente (para el Avatar en la UI).
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: function () {
                $words = explode(' ', (string) $this->name);
                $initials = '';
                foreach ($words as $w) {
                    $initials .= mb_substr($w, 0, 1);
                    if (mb_strlen($initials) >= 2) {
                        break;
                    }
                }
                return mb_strtoupper($initials ?: 'C');
            }
        );
    }
}