<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollAuditLog;
use App\Models\PayrollPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    public function __construct(
        private PayrollStatutoryService $statutoryService
    ) {}

    public function calculate(
        array $input,
        int $month,
        int $year
    ): array {
        $period = Carbon::create(
            $year,
            $month,
            1
        );

        $periodEnd =
            $period->copy()->endOfMonth();

        $daysInMonth =
            $period->daysInMonth;

        $basicSalary =
            $this->toCents(
                $input['basic_salary'] ?? 0
            );

        $bonus =
            $this->toCents(
                $input['bonus'] ?? 0
            );

        $gross =
            $basicSalary + $bonus;

        $leaveDaysHundredths =
            $this->toHundredths(
                $input['leave_days'] ?? 0
            );

        $lopDays = max(
            0,
            (int) ($input['lop_days'] ?? 0)
        );

        $lopDaysHundredths =
            $lopDays * 100;

        if (
            $leaveDaysHundredths >
            $daysInMonth * 100
        ) {
            throw ValidationException::withMessages([
                'leave_days' =>
                    "Leave days cannot exceed {$daysInMonth} days for the selected month.",
            ]);
        }

        if (
            $lopDays >
            $daysInMonth
        ) {
            throw ValidationException::withMessages([
                'lop_days' =>
                    "LOP days cannot exceed {$daysInMonth} days for the selected month.",
            ]);
        }

        if (
            $lopDaysHundredths >
            $leaveDaysHundredths
        ) {
            throw ValidationException::withMessages([
                'lop_days' =>
                    'LOP days cannot exceed total leave days.',
            ]);
        }

        $lopDeduction =
            $this->calculateLopDeductionCents(
                $basicSalary,
                $lopDays,
                $daysInMonth
            );

        $pfDeduction = 0;
        $professionalTax =
            $this->statutoryService
                ->professionalTaxCents(
                    $gross
                );

        $totalDeductions =
            $pfDeduction
            + $professionalTax
            + $lopDeduction;

        $net =
            $gross
            - $totalDeductions;

        if ($net < 0) {
            throw ValidationException::withMessages([
                'lop_days' =>
                    'Total deductions cannot exceed gross earnings.',
            ]);
        }

        $paidDays =
            $daysInMonth - $lopDays;

        return [
            'basic_salary' =>
                $this->fromCents(
                    $basicSalary
                ),

            'hra' => '0.00',

            'conveyance_allowance' =>
                '0.00',

            'medical_allowance' =>
                '0.00',

            'special_allowance' =>
                '0.00',

            'other_allowance' =>
                '0.00',

            'bonus' =>
                $this->fromCents(
                    $bonus
                ),

            'leave_days' =>
                $this->fromHundredths(
                    $leaveDaysHundredths
                ),

            'leave_deduction' =>
                '0.00',

            'lop_days' =>
                $lopDays,

            'lop_deduction' =>
                $this->fromCents(
                    $lopDeduction
                ),

            'paid_days' =>
                $this->fromHundredths(
                    $paidDays * 100
                ),

            'pf_deduction' =>
                $this->fromCents(
                    $pfDeduction
                ),

            'professional_tax' =>
                $this->fromCents(
                    $professionalTax
                ),

            'overtime_hours' =>
                '0.00',

            'overtime_amount' =>
                '0.00',

            'incentive' =>
                '0.00',

            'reimbursement' =>
                '0.00',

            'other_earnings' =>
                '0.00',

            'esi_deduction' =>
                '0.00',

            'tds' =>
                '0.00',

            'loan_deduction' =>
                '0.00',

            'other_deduction' =>
                '0.00',

            'gross_salary' =>
                $this->fromCents(
                    $gross
                ),

            'total_deductions' =>
                $this->fromCents(
                    $totalDeductions
                ),

            'net_salary' =>
                $this->fromCents(
                    $net
                ),
        ];
    }

    public function generate(
        Employee $employee,
        int $month,
        int $year,
        array $input,
        User $actor,
        ?string $ip = null
    ): Payroll {
        return DB::transaction(
            function () use (
                $employee,
                $month,
                $year,
                $input,
                $actor,
                $ip
            ) {
                if (
                    $employee->status !== 'Active'
                ) {
                    throw ValidationException::withMessages([
                        'employee_id' =>
                            'Payroll can only be generated for active employees.',
                    ]);
                }

                $alreadyExists =
                    Payroll::where(
                        'employee_id',
                        $employee->id
                    )
                    ->where(
                        'payroll_month',
                        $month
                    )
                    ->where(
                        'payroll_year',
                        $year
                    )
                    ->exists();

                if ($alreadyExists) {
                    throw ValidationException::withMessages([
                        'employee_id' =>
                            'Payroll already exists for this employee, month and year.',
                    ]);
                }

                $calculated =
                    $this->calculate(
                        $input,
                        $month,
                        $year
                    );

                $period = Carbon::create(
                    $year,
                    $month,
                    1
                );

                $payroll =
                    Payroll::create(
                        array_merge(
                            $calculated,
                            [
                                'payroll_number' =>
                                    $this->nextPayrollNumber(
                                        $month,
                                        $year
                                    ),

                                'employee_id' =>
                                    $employee->id,

                                'payroll_month' =>
                                    $month,

                                'payroll_year' =>
                                    $year,

                                'period_start' =>
                                    $period
                                        ->copy()
                                        ->startOfMonth(),

                                'period_end' =>
                                    $period
                                        ->copy()
                                        ->endOfMonth(),

                                'status' =>
                                    $input['status']
                                    ?? 'Generated',

                                'generated_at' =>
                                    now(),

                                'generated_by' =>
                                    $actor->id,
                            ]
                        )
                    );

                $this->audit(
                    $payroll,
                    $actor,
                    'payroll_created',
                    null,
                    $payroll->toArray(),
                    $ip
                );

                return $payroll;
            }
        );
    }

    public function update(
        Payroll $payroll,
        array $input,
        User $actor,
        ?string $ip = null
    ): Payroll {
        if (
            in_array(
                $payroll->status,
                ['Paid', 'Cancelled'],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Paid or cancelled payroll cannot be edited.',
            ]);
        }

        return DB::transaction(
            function () use (
                $payroll,
                $input,
                $actor,
                $ip
            ) {
                $old =
                    $payroll->toArray();

                $input['basic_salary'] =
                    array_key_exists(
                        'basic_salary',
                        $input
                    )
                    ? $input['basic_salary']
                    : $payroll->basic_salary;

                $input['bonus'] =
                    array_key_exists(
                        'bonus',
                        $input
                    )
                    ? $input['bonus']
                    : $payroll->bonus;

                $input['leave_days'] =
                    array_key_exists(
                        'leave_days',
                        $input
                    )
                    ? $input['leave_days']
                    : (
                        $payroll->leave_days
                        ?? $payroll->lop_days
                        ?? 0
                    );

                $input['lop_days'] =
                    array_key_exists(
                        'lop_days',
                        $input
                    )
                    ? $input['lop_days']
                    : (
                        $payroll->lop_days
                        ?? 0
                    );

                $calculated =
                    $this->calculate(
                        $input,
                        (int)
                        $payroll->payroll_month,
                        (int)
                        $payroll->payroll_year
                    );

                $payroll->update(
                    array_merge(
                        $calculated,
                        [
                            'status' =>
                                $input['status']
                                ?? $payroll->status,
                        ]
                    )
                );

                $updatedPayroll =
                    $payroll->fresh();

                $this->audit(
                    $updatedPayroll,
                    $actor,
                    'payroll_updated',
                    $old,
                    $updatedPayroll->toArray(),
                    $ip
                );

                return $updatedPayroll;
            }
        );
    }

    public function markPaid(
        Payroll $payroll,
        array $input,
        User $actor,
        ?string $ip = null
    ): Payroll {
        if (
            $payroll->status === 'Paid'
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'This payroll is already marked as paid.',
            ]);
        }

        if (
            $payroll->status === 'Cancelled'
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Cancelled payroll cannot be paid.',
            ]);
        }

        return DB::transaction(
            function () use (
                $payroll,
                $input,
                $actor,
                $ip
            ) {
                $old =
                    $payroll->toArray();

                PayrollPayment::create([
                    'payroll_id' =>
                        $payroll->id,

                    'amount' =>
                        $payroll->net_salary,

                    'payment_date' =>
                        $input['payment_date'],

                    'payment_method' =>
                        $input['payment_method'],

                    'transaction_reference' =>
                        $input['payment_reference']
                        ?? null,

                    'notes' =>
                        $input['payment_notes']
                        ?? null,

                    'recorded_by' =>
                        $actor->id,
                ]);

                $payroll->update([
                    'status' =>
                        'Paid',

                    'payment_date' =>
                        $input['payment_date'],

                    'payment_method' =>
                        $input['payment_method'],

                    'payment_reference' =>
                        $input['payment_reference']
                        ?? null,

                    'payment_notes' =>
                        $input['payment_notes']
                        ?? null,

                    'paid_by' =>
                        $actor->id,
                ]);

                $updatedPayroll =
                    $payroll->fresh();

                $this->audit(
                    $updatedPayroll,
                    $actor,
                    'payroll_marked_paid',
                    $old,
                    $updatedPayroll->toArray(),
                    $ip
                );

                return $updatedPayroll;
            }
        );
    }

    public function audit(
        ?Payroll $payroll,
        User $actor,
        string $action,
        ?array $old,
        ?array $new,
        ?string $ip
    ): void {
        PayrollAuditLog::create([
            'payroll_id' =>
                $payroll?->id,

            'user_id' =>
                $actor->id,

            'action' =>
                $action,

            'old_values' =>
                $old,

            'new_values' =>
                $new,

            'ip_address' =>
                $ip,

            'created_at' =>
                now(),
        ]);
    }

    private function nextPayrollNumber(
        int $month,
        int $year
    ): string {
        $sequence =
            Payroll::where(
                'payroll_year',
                $year
            )
            ->where(
                'payroll_month',
                $month
            )
            ->lockForUpdate()
            ->count() + 1;

        return sprintf(
            'JNX-PAY-%04d-%02d-%04d',
            $year,
            $month,
            $sequence
        );
    }

    private function calculateLopDeductionCents(
        int $basicSalaryCents,
        int $lopDays,
        int $daysInMonth
    ): int {
        if (
            $lopDays <= 0
            || $daysInMonth <= 0
        ) {
            return 0;
        }

        return intdiv(
            (
                $basicSalaryCents
                * $lopDays
            )
            + intdiv(
                $daysInMonth,
                2
            ),
            $daysInMonth
        );
    }

    private function toCents(
        mixed $value
    ): int {
        $raw =
            trim(
                (string)
                ($value ?? '0')
            );

        $negative =
            str_starts_with(
                $raw,
                '-'
            );

        $normalized =
            preg_replace(
                '/[^0-9.]/',
                '',
                $raw
            ) ?: '0';

        [$whole, $fraction] =
            array_pad(
                explode(
                    '.',
                    $normalized,
                    2
                ),
                2,
                ''
            );

        $fraction =
            substr(
                str_pad(
                    $fraction,
                    3,
                    '0'
                ),
                0,
                3
            );

        $cents =
            (
                (int) $whole * 100
            )
            + (int) substr(
                $fraction,
                0,
                2
            );

        if (
            (int) $fraction[2] >= 5
        ) {
            $cents++;
        }

        return $negative
            ? -$cents
            : $cents;
    }

    private function fromCents(
        int $cents
    ): string {
        $negative =
            $cents < 0;

        $absolute =
            abs($cents);

        $formatted =
            intdiv(
                $absolute,
                100
            )
            . '.'
            . str_pad(
                (string)
                ($absolute % 100),
                2,
                '0',
                STR_PAD_LEFT
            );

        return $negative
            ? '-' . $formatted
            : $formatted;
    }

    private function toHundredths(
        mixed $value
    ): int {
        $raw =
            trim(
                (string)
                ($value ?? '0')
            );

        $normalized =
            preg_replace(
                '/[^0-9.]/',
                '',
                $raw
            ) ?: '0';

        [$whole, $fraction] =
            array_pad(
                explode(
                    '.',
                    $normalized,
                    2
                ),
                2,
                ''
            );

        $fraction =
            substr(
                str_pad(
                    $fraction,
                    2,
                    '0'
                ),
                0,
                2
            );

        return (
            (int) $whole * 100
        ) + (int) $fraction;
    }

    private function fromHundredths(
        int $value
    ): string {
        return intdiv(
            $value,
            100
        )
        . '.'
        . str_pad(
            (string)
            ($value % 100),
            2,
            '0',
            STR_PAD_LEFT
        );
    }
}