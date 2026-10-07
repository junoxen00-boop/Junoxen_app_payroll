<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollProfile extends Model
{
    protected $fillable = [
        'employee_id',
        'employment_type',
        'payroll_status',
        'pay_frequency',
        'start_date',
        'end_date',
        'default_basic_salary',
        'default_bonus',
        'payroll_notes',
        'account_holder_name',
        'bank_name',
        'account_number',
        'routing_identifier',
        'bank_branch',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'default_basic_salary' => 'decimal:2',
            'default_bonus' => 'decimal:2',
            'account_holder_name' => 'encrypted',
            'bank_name' => 'encrypted',
            'account_number' => 'encrypted',
            'routing_identifier' => 'encrypted',
            'bank_branch' => 'encrypted',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
