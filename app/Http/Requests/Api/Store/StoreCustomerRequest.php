<?php

namespace App\Http\Requests\Api\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'category' => ['required', 'string', 'in:A,A+,B,C'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique('customers', 'email')],
            'tax_id' => ['required', 'string'],
            'payment_terms' => ['required', 'string'],
            'credit_limit' => ['required', 'numeric'],
            'current_balance' => ['required', 'numeric'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address' => ['required', 'string'],
            'territory' => ['required', 'string', 'exists:territories,name']
        ];
    }
}
