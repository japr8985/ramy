<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException; 
final class CreateUnitRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'name'),
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units', 'symbol'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'El nombre de la unidad es requerido.',
            'name.max'        => 'El nombre no puede exceder los 50 caracteres.',
            'name.unique'     => 'Ya existe una unidad registrada con ese nombre.',
            'symbol.required' => 'El símbolo de la unidad es requerido.',
            'symbol.max'      => 'El símbolo no puede exceder los 10 caracteres.',
            'symbol.unique'   => 'Ya existe una unidad con ese símbolo.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->validationError($validator->errors())
        );
    }
}