<?php

declare(strict_types=1);

namespace App\Modules\Settings\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException; 

final class UpdateSettingsRequest extends FormRequest
{
    use ApiResponse;
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
            'business_name'  => ['required', 'string', 'max:255'],
            'tax_id'         => ['required', 'string', 'max:50'],
            'currency'       => ['required', 'string', 'max:10'],
            'exchange_rate'  => ['required', 'numeric', 'min:0.01'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'El nombre de la empresa es requerido.',
            'tax_id.required'        => 'El RIF / Identificación Fiscal es requerido.',
            'currency.required'      => 'La moneda predeterminada es requerida.',
            'exchange_rate.required' => 'La tasa de cambio es requerida.',
            'exchange_rate.numeric'  => 'La tasa de cambio debe ser un número válido.',
            'exchange_rate.min'      => 'La tasa de cambio debe ser mayor a 0.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->validationError($validator->errors())
        );
    }
}