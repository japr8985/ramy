<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RecordCashMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type'     => ['required', 'string', Rule::in(['in', 'out'])],
            'currency' => ['required', 'string', Rule::in(['USD', 'VES'])],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'concept'  => ['required', 'string', 'max:255'],
            'notes'    => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'     => 'Indique el tipo de movimiento (in / out).',
            'type.in'           => 'El tipo debe ser ingreso (in) o egreso (out).',
            'currency.required' => 'Especifique la moneda (USD o VES).',
            'currency.in'       => 'Moneda inválida. Use USD o VES.',
            'amount.required'   => 'El monto del movimiento es obligatorio.',
            'amount.min'        => 'El monto debe ser estrictamente mayor a 0.',
            'concept.required'  => 'El motivo o concepto del movimiento es obligatorio.',
        ];
    }
}