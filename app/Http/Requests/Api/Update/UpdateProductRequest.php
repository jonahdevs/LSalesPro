<?php

namespace App\Http\Requests\Api\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::guard('sanctum')->user();
        return $user && $user->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ['nullable', 'string', 'max:255'],
            "sku" => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product)],
            "category" => ['nullable', 'string', 'exists:categories,name'],
            'description' => ['nullable', 'string', 'min:10'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'packaging' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string'],
            'min_order_quantity' => ['nullable', 'integer', 'min:1'],
            'reorder_level' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
