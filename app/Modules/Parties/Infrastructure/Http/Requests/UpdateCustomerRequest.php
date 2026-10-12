<?php

declare(strict_types=1);

namespace App\Modules\Parties\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCustomerRequest extends FormRequest
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
        $customerId = (string) $this->route('customer');

        return [
            'rif_ci' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'rif_ci')
                    ->ignore($customerId)
                    ->whereNull('deleted_at'),
            ],
            'name'    => ['required', 'string', 'max:150'],
            'type'    => ['required', 'string', Rule::in(['Individual', 'Corporate'])],
            'email'   => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('customers', 'email')
                    ->ignore($customerId)
                    ->whereNull('deleted_at'),
            ],
            'phone'   => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rif_ci.required' => 'La Cédula o RIF es obligatoria.',
            'rif_ci.unique'   => 'Este RIF o Cédula ya pertenece a otro cliente.',
            'name.required'   => 'El nombre del cliente es obligatorio.',
            'type.required'   => 'El tipo de cliente es obligatorio.',
            'type.in'         => 'El tipo de cliente debe ser Individual o Corporate.',
            'email.email'     => 'Indique un formato de correo electrónico válido.',
            'email.unique'    => 'Este correo ya pertenece a otro cliente registrado.',
        ];
    }
}