<?php

namespace App\Http\Requests\Api\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ['required', 'string', 'max:255'],
            "sku" => ['required', 'string', 'max:100', 'unique:products,sku'],
            "category" => ['required', 'string', 'exists:categories,name'],
            'description' => ['required', 'string', 'min:10'],
            'price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'packaging' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string'],
            'min_order_quantity' => ['nullable', 'integer', 'min:1'],
            'reorder_level' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
