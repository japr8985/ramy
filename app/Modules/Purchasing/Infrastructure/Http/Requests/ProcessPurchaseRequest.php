<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProcessPurchaseRequest extends FormRequest
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
            'supplier_id'            => ['required', 'uuid', 'exists:suppliers,id'],
            'purchase_date'          => ['required', 'date'],
            'due_date'               => ['nullable', 'date'],
            'invoice_number'         => ['nullable', 'string', 'max:100'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
            'proof_image'            => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'items.*.selling_price'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'supplier_id.required'       => 'Debe asociar un proveedor a la compra.',
            'supplier_id.exists'         => 'El proveedor seleccionado no existe.',
            'purchase_date.required'     => 'La fecha de compra es obligatoria.',
            'items.required'             => 'Debe incluir al menos un producto en la orden.',
            'items.min'                  => 'La compra debe tener al menos un renglón.',
            'items.*.product_id.exists'  => 'Uno de los productos indicados no existe en el catálogo.',
            'items.*.quantity.min'       => 'La cantidad ingresada debe ser mínimo 1 unidad.',
            'items.*.unit_price.min'     => 'El costo unitario no puede ser negativo.',
        ];
    }
}