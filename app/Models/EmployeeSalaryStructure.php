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

    $lopDaysHundredths =
        $this->toHundredths(
            $input['lop_days'] ?? 0
        );

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
        $lopDaysHundredths >
        $daysInMonth * 100
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
            $lopDaysHundredths,
            $daysInMonth
        );

    $pfDeduction =
        $this->statutoryService
            ->providentFundCents(
                $basicSalary,
                $periodEnd
            );

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

    $paidDaysHundredths =
        ($daysInMonth * 100)
        - $lopDaysHundredths;

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
            $this->fromHundredths(
                $lopDaysHundredths
            ),

        'lop_deduction' =>
            $this->fromCents(
                $lopDeduction
            ),

        'paid_days' =>
            $this->fromHundredths(
                $paidDaysHundredths
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