<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    /**
     * Fields allowed for mass assignment.
     *
     * Existing fields are retained for database
     * and PayrollService compatibility.
     *
     * lop_days has been added for the new payslip.
     */
    protected $fillable = [
        'payroll_number',

        'employee_id',

        'payroll_month',

        'payroll_year',

        'period_start',

        'period_end',

        /*
        |--------------------------------------------------------------------------
        | Existing salary fields
        |--------------------------------------------------------------------------
        |
        | These remain because your existing PayrollService and
        | database may still use them internally to calculate salary.
        |
        */

        'basic_salary',

        'hra',

        'conveyance_allowance',

        'medical_allowance',

        'special_allowance',

        'other_allowance',

        'overtime_hours',

        'overtime_amount',

        /*
        |--------------------------------------------------------------------------
        | Payslip Earnings
        |--------------------------------------------------------------------------
        */

        'bonus',

        /*
        |--------------------------------------------------------------------------
        | Existing earning fields retained for compatibility
        |--------------------------------------------------------------------------
        */

        'incentive',

        'reimbursement',

        'other_earnings',

        /*
        |--------------------------------------------------------------------------
        | Calculated Gross Salary
        |--------------------------------------------------------------------------
        */

        'gross_salary',

        /*
        |--------------------------------------------------------------------------
        | Payslip Deductions
        |--------------------------------------------------------------------------
        */

        'pf_deduction',

        'professional_tax',

        'leave_deduction',
        'leave_days',

        /*
        |--------------------------------------------------------------------------
        | Loss Of Pay
        |--------------------------------------------------------------------------
        */

        'lop_days',
        'lop_deduction',
        'paid_days',

        /*
        |--------------------------------------------------------------------------
        | Existing deduction fields retained for compatibility
        |--------------------------------------------------------------------------
        */

        'esi_deduction',

        'tds',

        'loan_deduction',

        'other_deduction',

        /*
        |--------------------------------------------------------------------------
        | Calculated Totals
        |--------------------------------------------------------------------------
        */

        'total_deductions',

        'net_salary',

        /*
        |--------------------------------------------------------------------------
        | Payroll Status
        |--------------------------------------------------------------------------
        */

        'status',

        'generated_at',

        'generated_by',

        /*
        |--------------------------------------------------------------------------
        | Payment Information
        |--------------------------------------------------------------------------
        */

        'payment_date',

        'payment_method',

        'payment_reference',

        'payment_notes',

        'paid_by',
    ];

    /**
     * Model casts.
     */
    protected function casts(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'period_start' => 'date',

            'period_end' => 'date',

            'generated_at' => 'datetime',

            'payment_date' => 'date',

            /*
            |--------------------------------------------------------------------------
            | Existing Salary Values
            |--------------------------------------------------------------------------
            */

            'basic_salary' => 'decimal:2',

            'hra' => 'decimal:2',

            'conveyance_allowance' => 'decimal:2',

            'medical_allowance' => 'decimal:2',

            'special_allowance' => 'decimal:2',

            'other_allowance' => 'decimal:2',

            'overtime_hours' => 'decimal:2',

            'overtime_amount' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Payslip Earnings
            |--------------------------------------------------------------------------
            */

            'bonus' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Existing Earnings
            |--------------------------------------------------------------------------
            */

            'incentive' => 'decimal:2',

            'reimbursement' => 'decimal:2',

            'other_earnings' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Gross Salary
            |--------------------------------------------------------------------------
            */

            'gross_salary' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Payslip Deductions
            |--------------------------------------------------------------------------
            */

            'pf_deduction' => 'decimal:2',

            'professional_tax' => 'decimal:2',

            'leave_deduction' => 'decimal:2',
            'leave_days' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | LOP / Paid Days
            |--------------------------------------------------------------------------
            */

            'lop_days' => 'integer',
            'lop_deduction' => 'decimal:2',
            'paid_days' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Existing Deductions
            |--------------------------------------------------------------------------
            */

            'esi_deduction' => 'decimal:2',

            'tds' => 'decimal:2',

            'loan_deduction' => 'decimal:2',

            'other_deduction' => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Calculated Values
            |--------------------------------------------------------------------------
            */

            'total_deductions' => 'decimal:2',

            'net_salary' => 'decimal:2',
        ];
    }

    /**
     * Employee belonging to this payroll.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class
        );
    }

    /**
     * User who generated payroll.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }

    /**
     * User who marked payroll as paid.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'paid_by'
        );
    }

    /**
     * Payroll payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(
            PayrollPayment::class
        );
    }

    /**
     * Payroll audit history.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(
            PayrollAuditLog::class
        );
    }
}