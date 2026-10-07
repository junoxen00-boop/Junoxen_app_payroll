<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'Admin';
    }

    public function rules(): array
    {
        return [
            'basic_salary' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'bonus' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'leave_days' => [
                'nullable',
                'numeric',
                'min:0',
                'max:31',
                'multiple_of:0.5',
            ],

            'lop_days' => [
                'nullable',
                'numeric',
                'min:0',
                'max:31',
                'multiple_of:0.5',
            ],

            'status' => [
                'nullable',
                'in:Draft,Generated',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'basic_salary.required' =>
                'Basic Salary is required.',

            'basic_salary.min' =>
                'Basic Salary cannot be negative.',

            'leave_days.multiple_of' =>
                'Leave Days must be entered in half-day increments such as 0.5, 1, 1.5 or 2.',

            'lop_days.multiple_of' =>
                'LOP Days must be entered in half-day increments such as 0.5, 1, 1.5 or 2.',
        ];
    }
}