<?php

declare(strict_types=1);

namespace App\Modules\Settings\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Atributos asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'key'   => 'string',
        'value' => 'string',
    ];
}