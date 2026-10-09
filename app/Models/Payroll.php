<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_number',

        'employee_id',

        'payroll_month',

        'payroll_year',

        'period_start',

        'period_end',

        'basic_salary',

        'hra',

        'conveyance_allowance',

        'medical_allowance',

        'special_allowance',

        'other_allowance',

        'overtime_hours',

        'overtime_amount',

        'bonus',

        'incentive',

        'reimbursement',

        'other_earnings',

        'gross_salary',

        'pf_deduction',

        'professional_tax',

        'leave_deduction',

        'leave_days',

        'lop_days',

        'lop_deduction',

        'paid_days',

        'attendance_import_id',

        'attendance_late_minutes',

        'attendance_deduction',

        'esi_deduction',

        'tds',

        'loan_deduction',

        'other_deduction',

        'total_deductions',

        'net_salary',

        'status',

        'generated_at',

        'generated_by',

        'payment_date',

        'payment_method',

        'payment_reference',

        'payment_notes',

        'paid_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' =>
                'date',

            'period_end' =>
                'date',

            'generated_at' =>
                'datetime',

            'payment_date' =>
                'date',

            /*
            |--------------------------------------------------------------------------
            | Leave values
            |--------------------------------------------------------------------------
            */

            'leave_days' =>
                'decimal:2',

            'lop_days' =>
                'decimal:2',

            'paid_days' =>
                'decimal:2',

            'attendance_late_minutes' =>
                'integer',

            'attendance_deduction' =>
                'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Monetary values
            |--------------------------------------------------------------------------
            */

            'basic_salary' =>
                'decimal:2',

            'hra' =>
                'decimal:2',

            'conveyance_allowance' =>
                'decimal:2',

            'medical_allowance' =>
                'decimal:2',

            'special_allowance' =>
                'decimal:2',

            'other_allowance' =>
                'decimal:2',

            'overtime_hours' =>
                'decimal:2',

            'overtime_amount' =>
                'decimal:2',

            'bonus' =>
                'decimal:2',

            'incentive' =>
                'decimal:2',

            'reimbursement' =>
                'decimal:2',

            'other_earnings' =>
                'decimal:2',

            'gross_salary' =>
                'decimal:2',

            'pf_deduction' =>
                'decimal:2',

            'professional_tax' =>
                'decimal:2',

            'leave_deduction' =>
                'decimal:2',

            'lop_deduction' =>
                'decimal:2',

            'esi_deduction' =>
                'decimal:2',

            'tds' =>
                'decimal:2',

            'loan_deduction' =>
                'decimal:2',

            'other_deduction' =>
                'decimal:2',

            'total_deductions' =>
                'decimal:2',

            'net_salary' =>
                'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    public function attendanceImport(): BelongsTo
    {
        return $this->belongsTo(
            AttendanceImport::class
        );
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'paid_by'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            PayrollPayment::class
        );
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(
            PayrollAuditLog::class
        );
    }
}