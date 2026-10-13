<?php

declare(strict_types=1);

namespace App\Modules\Receivables\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RecordPaymentRequest extends FormRequest
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
            'amount_usd'     => ['required', 'numeric', 'min:0.01'],
            'exchange_rate'  => ['required', 'numeric', 'min:0.0001'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount_usd.required'     => 'El monto a abonar en dólares es obligatorio.',
            'amount_usd.min'          => 'El monto a abonar debe ser superior a 0.',
            'exchange_rate.required'  => 'La tasa cambiaria referencial es requerida.',
            'payment_date.required'   => 'La fecha del abono es requerida.',
            'payment_method.required' => 'Indique el método de cobro utilizado.',
        ];
    }
}