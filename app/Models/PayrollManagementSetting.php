<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollManagementSetting extends Model
{
    protected $fillable = [
        'company_name',
        'pay_frequency',
        'currency',
        'default_payment_method',
        'lop_calculation_basis',
        'payroll_year',
        'payslip_footer',
        'attendance_payroll_enabled',
        'default_shift_start',
        'default_shift_end',
        'grace_minutes',
        'grace_deduction_mode',
        'standard_work_minutes_per_day',
        'deduct_late_arrival',
        'late_deduction_rounding',
    ];

    protected function casts(): array
    {
        return [
            'attendance_payroll_enabled' => 'boolean',
            'deduct_late_arrival' => 'boolean',
            'grace_minutes' => 'integer',
            'standard_work_minutes_per_day' => 'integer',
        ];
    }
}