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
}