<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'shipping_first_name' => 'nullable|string|max:100',
            'shipping_last_name' => 'nullable|string|max:100',
            'shipping_address_1' => 'nullable|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_postcode' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|size:2',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'address_1.required' => 'L\'adresse est obligatoire.',
            'city.required' => 'La ville est obligatoire.',
            'postcode.required' => 'Le code postal est obligatoire.',
            'country.required' => 'Le pays est obligatoire.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('shipping_address_1')) {
            $this->merge([
                'shipping_first_name' => null,
                'shipping_last_name' => null,
                'shipping_address_1' => null,
                'shipping_address_2' => null,
                'shipping_city' => null,
                'shipping_postcode' => null,
                'shipping_country' => null,
            ]);
        }
    }
}
