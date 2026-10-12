<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException; 
final class CreateProductRequest extends FormRequest
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
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku'),
            ],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'string', 'uuid', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'unit_id'        => ['required', 'string', 'uuid', Rule::exists('units', 'id')],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'quantity'       => ['required', 'integer', 'min:0'],
            'min_stock'      => ['required', 'integer', 'min:0'],
            'is_active'      => ['sometimes', 'boolean'],
            'description'    => ['nullable', 'string', 'max:500'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sku.required'            => 'El código de barras / SKU es obligatorio.',
            'sku.unique'              => 'Este código de barras ya se encuentra registrado.',
            'name.required'           => 'El nombre del producto es obligatorio.',
            'category_id.required'    => 'Debe seleccionar una categoría.',
            'category_id.exists'      => 'La categoría seleccionada no existe o está inactiva.',
            'unit_id.required'        => 'Debe seleccionar una unidad de medida.',
            'unit_id.exists'          => 'La unidad de medida seleccionada no existe.',
            'purchase_price.required' => 'El precio de costo es obligatorio.',
            'purchase_price.min'      => 'El precio de costo no puede ser negativo.',
            'selling_price.required'  => 'El precio de venta es obligatorio.',
            'selling_price.min'       => 'El precio de venta no puede ser negativo.',
            'quantity.required'       => 'La existencia actual es obligatoria.',
            'min_stock.required'      => 'El stock mínimo es obligatorio.',
        ];
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->validationError($validator->errors())
        );
    }
}