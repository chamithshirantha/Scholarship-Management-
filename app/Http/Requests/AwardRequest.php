<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'terms_and_conditions' => 'required|string|min:10',
            'disbursement_schedule' => 'sometimes|array',
            'disbursement_schedule.*.cost_category_id' => 'required_with:disbursement_schedule|exists:cost_categories,id',
            'disbursement_schedule.*.amount' => 'required_with:disbursement_schedule|numeric|min:0',
            'disbursement_schedule.*.scheduled_date' => 'required_with:disbursement_schedule|date|after:today'
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required' => 'The total amount is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'start_date.required' => 'The start date is required.',
            'start_date.after' => 'The start date must be in the future.',
            'end_date.required' => 'The end date is required.',
            'end_date.after' => 'The end date must be after the start date.',
            'terms_and_conditions.required' => 'Terms and conditions are required.',
            'terms_and_conditions.min' => 'Terms and conditions must be at least 10 characters.'
        ];
    }
}
