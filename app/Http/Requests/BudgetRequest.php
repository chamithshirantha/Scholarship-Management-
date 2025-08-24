<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
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
            'categories' => 'required|array|min:1',
            'categories.*.cost_category_id' => 'required|exists:cost_categories,id',
            'categories.*.amount' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'At least one budget category is required',
            'categories.*.cost_category_id.required' => 'Cost category ID is required',
            'categories.*.cost_category_id.exists' => 'The selected cost category does not exist',
            'categories.*.amount.required' => 'Category amount is required',
            'categories.*.amount.numeric' => 'Category amount must be a number',
            'categories.*.amount.min' => 'Category amount must be at least 0'
        ];
    }
}
