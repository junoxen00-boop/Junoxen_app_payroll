<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'Admin';
    }

    public function rules(): array
    {
        return [
            'attendance_file' => [
                'required',
                'file',
                'mimes:xlsx',
                'max:10240',
            ],
        ];
    }
}