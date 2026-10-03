<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'Admin';
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'payroll_month' => [
                'required',
                'integer',
                'between:1,12',
            ],

            'payroll_year' => [
                'required',
                'integer',
                'between:2000,2100',
            ],

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
            ],

            'lop_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:31',
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

            'leave_days.max' =>
                'Leave days cannot exceed 31.',

            'lop_days.max' =>
                'LOP days cannot exceed 31.',
        ];
    }
}