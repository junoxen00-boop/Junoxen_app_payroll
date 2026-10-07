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
    ];
}
