<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'     => 'string',
        'name'   => 'string',
        'symbol' => 'string',
    ];
}