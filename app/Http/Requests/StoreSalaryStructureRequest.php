<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'Admin';
    }

    public function rules(): array
    {
        $money = ['nullable', 'numeric', 'min:0', 'max:9999999999.99'];

        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'basic_salary' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'hra' => $money,
            'conveyance_allowance' => $money,
            'medical_allowance' => $money,
            'special_allowance' => $money,
            'other_allowance' => $money,
            'overtime_rate' => $money,
            'pf_applicable' => ['required', 'boolean'],
            'pf_wage_basis' => $money,
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'status' => ['required', 'in:Active,Inactive'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
