<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SaveSupplierRequest extends FormRequest
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
        $supplierId = $this->route('supplier') ?? $this->route('id');

        return [
            'name'                => ['required', 'string', 'max:255'],
            'contact_person'      => ['required', 'string', 'max:255'],
            'email'               => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('suppliers', 'email')
                    ->ignore($supplierId)
                    ->whereNull('deleted_at'),
            ],
            'phone'               => ['nullable', 'string', 'max:50'],
            'address'             => ['nullable', 'string', 'max:500'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'selected_products'   => ['nullable', 'array'],
            'selected_products.*' => ['exists:products,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'           => 'El nombre de la empresa proveedora es obligatorio.',
            'contact_person.required' => 'Debe indicar una persona o contacto directo.',
            'email.email'             => 'Indique un correo electrónico válido.',
            'email.unique'            => 'Este correo electrónico ya pertenece a otro proveedor registrado.',
            'selected_products.*.exists' => 'Uno de los productos seleccionados no existe en el catálogo.',
        ];
    }
}