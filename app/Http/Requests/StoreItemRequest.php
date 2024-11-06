<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'The selected category is invalid',
            'name.required' => 'The item name is required',
            'name.max' => 'The item name must not exceed 255 characters',
            'stock.required' => 'Please specify the initial stock',
            'stock.integer' => 'The stock must be a whole number',
            'stock.min' => 'The stock cannot be negative',
        ];
    }
}
