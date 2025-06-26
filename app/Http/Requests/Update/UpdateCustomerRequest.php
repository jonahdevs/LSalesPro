<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'in:A,A+,B,C'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'email', Rule::unique('customers', 'email')->ignore($this->customer)],
            'tax_id' => ['nullable', 'string'],
            'payment_terms' => ['nullable', 'string'],
            'credit_limit' => ['nullable', 'numeric'],
            'current_balance' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string'],
            'territory' => ['nullable', 'string', 'exists:territories,name']
        ];

    }
}
