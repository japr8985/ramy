<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CloseCashShiftRequest extends FormRequest
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
            'actual_cash_usd' => ['required', 'numeric', 'min:0'],
            'actual_cash_ves' => ['required', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'actual_cash_usd.required' => 'Indique el monto contado en dólares (USD).',
            'actual_cash_usd.min'      => 'El conteo en USD no puede ser negativo.',
            'actual_cash_ves.required' => 'Indique el monto contado en bolívares (VES).',
            'actual_cash_ves.min'      => 'El conteo en VES no puede ser negativo.',
        ];
    }
}