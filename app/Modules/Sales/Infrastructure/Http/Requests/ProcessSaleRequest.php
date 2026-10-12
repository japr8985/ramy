<?php

declare(strict_types=1);

namespace App\Modules\Sales\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProcessSaleRequest extends FormRequest
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
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'uuid', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],

            'customer_id'            => ['nullable', 'uuid', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'payment_method'         => ['required', 'string', Rule::in(['cash', 'card', 'credit'])],
            'total_discount'         => ['nullable', 'numeric', 'min:0'],
            'exchange_rate'          => ['required', 'numeric', 'min:0.0001'],
            'initial_payment'        => ['nullable', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required'              => 'Debe incluir al menos un producto en el carrito.',
            'items.min'                   => 'Debe incluir al menos un producto en el carrito.',
            'items.*.product_id.required' => 'El identificador del producto es obligatorio.',
            'items.*.product_id.exists'   => 'Uno de los productos seleccionados no existe en el catálogo.',
            'items.*.quantity.required'   => 'La cantidad del ítem es requerida.',
            'items.*.quantity.min'        => 'La cantidad mínima vendible por producto es 1.',
            'payment_method.required'     => 'Indique el método de pago (efectivo, tarjeta o crédito).',
            'payment_method.in'           => 'Método de pago no válido.',
            'exchange_rate.required'      => 'La tasa de cambio del día es requerida.',
        ];
    }
}