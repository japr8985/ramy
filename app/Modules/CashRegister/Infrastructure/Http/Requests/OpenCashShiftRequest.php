<?php

declare(strict_types=1);

namespace App\Modules\CashRegister\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OpenCashShiftRequest extends FormRequest
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
            'initial_cash_usd' => ['nullable', 'numeric', 'min:0'],
            'initial_cash_ves' => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'initial_cash_usd.min' => 'El fondo inicial en USD no puede ser negativo.',
            'initial_cash_ves.min' => 'El fondo inicial en VES no puede ser negativo.',
        ];
    }
}