<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
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
            'scholarship_id' => 'required|exists:scholarships,id',
            'personal_statement' => 'required|string|min:10|max:1000',
            'financial_information' => 'required|array',
            'financial_information.income' => 'required|numeric|min:0',
            'financial_information.expenses' => 'required|numeric|min:0',
            'academic_records' => 'required|array',
            'references' => 'required|array|min:2',
            'references.*.name' => 'required|string',
            'references.*.email' => 'required|email',
            'references.*.relationship' => 'required|string',
        ];
    }
}
