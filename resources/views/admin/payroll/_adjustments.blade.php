@php
    $editingPayroll = isset($payroll);

    $existingLeaveDays = $editingPayroll
        ? (float) (
            $payroll->leave_days
            ?? $payroll->lop_days
            ?? 0
        )
        : 0;
@endphp

<div class="row g-3" id="payrollAdjustments">

    <div class="col-12">
        <h5 class="fw-bold mb-1">
            Payroll Earnings & Statutory Deductions
        </h5>

        <p class="text-muted small mb-0">
            Enter Basic Salary, Bonus and leave information.
            PF, Professional Tax, LOP Deduction,
            Total Deductions and Total Net Payable
            are calculated securely on the server.
        </p>
    </div>

    {{-- BASIC SALARY --}}
    <div class="col-md-4">
        <label class="form-label">
            Basic Salary *
        </label>

        <div class="input-group">
            <span class="input-group-text">
                ₹
            </span>

            <input
                type="number"
                step="0.01"
                min="0"
                name="basic_salary"
                class="form-control"
                value="{{ old(
                    'basic_salary',
                    $editingPayroll
                        ? $payroll->basic_salary
                        : 0
                ) }}"
                required
            >
        </div>

        <div class="form-text">
            Enter the employee's Basic Salary
            for this payroll period.
        </div>
    </div>

    {{-- BONUS --}}
    <div class="col-md-4">
        <label class="form-label">
            Bonus
        </label>

        <div class="input-group">
            <span class="input-group-text">
                ₹
            </span>

            <input
                type="number"
                step="0.01"
                min="0"
                name="bonus"
                class="form-control"
                value="{{ old(
                    'bonus',
                    $editingPayroll
                        ? $payroll->bonus
                        : 0
                ) }}"
            >
        </div>

        <div class="form-text">
            Bonus is added to Gross Earnings.
        </div>
    </div>

    {{-- LEAVE DAYS --}}
    <div class="col-md-4">
        <label class="form-label">
            Leave Days
        </label>

        <input
            type="number"
            step="0.5"
            min="0"
            max="31"
            name="leave_days"
            id="leaveDays"
            class="form-control"
            value="{{ old(
                'leave_days',
                $existingLeaveDays
            ) }}"
        >

        <div class="form-text">
            Total leave taken in the payroll month.
        </div>
    </div>

    {{-- LOP DAYS --}}
    <div class="col-md-4">
        <label class="form-label">
            LOP Days
        </label>

        <input
            type="number"
            step="1"
            min="0"
            max="31"
            name="lop_days"
            id="lopDays"
            class="form-control"
            value="{{ old(
                'lop_days',
                $editingPayroll
                    ? ($payroll->lop_days ?? 0)
                    : 0
            ) }}"
        >

        <div class="form-text">
            Enter only unpaid leave days.
            LOP cannot exceed Leave Days.
        </div>
    </div>

    {{-- PROFESSIONAL TAX --}}
    <div class="col-md-4">
        <label class="form-label">
            Professional Tax
        </label>

        <input
            type="text"
            class="form-control bg-light"
            readonly
            value="{{ $editingPayroll
                ? '₹' . number_format(
                    (float)
                    $payroll->professional_tax,
                    2
                )
                : 'Calculated automatically'
            }}"
        >
    </div>

  {{-- PROVIDENT FUND --}}
<div class="col-md-4">
    <label class="form-label">
        Provident Fund
    </label>

    <input
        type="text"
        class="form-control bg-light"
        value="Disabled"
        readonly
    >

    <div class="form-text">
        Provident Fund is currently disabled.
    </div>
</div>

    {{-- LOP DEDUCTION --}}
    <div class="col-md-4">
        <label class="form-label">
            LOP Deduction
        </label>

        <input
            type="text"
            class="form-control bg-light"
            readonly
            value="{{ $editingPayroll
                ? '₹' . number_format(
                    (float)
                    ($payroll->lop_deduction ?? 0),
                    2
                )
                : 'Calculated automatically'
            }}"
        >
    </div>

    {{-- PAID DAYS --}}
    <div class="col-md-4">
        <label class="form-label">
            Paid Days
        </label>

        <input
            type="text"
            class="form-control bg-light"
            readonly
            value="{{ $editingPayroll
                ? number_format(
                    (float)
                    ($payroll->paid_days ?? 0),
                    2
                )
                : 'Calculated automatically'
            }}"
        >
    </div>

    {{-- TOTAL DEDUCTIONS --}}
    <div class="col-md-4">
        <label class="form-label">
            Total Deductions
        </label>

        <input
            type="text"
            class="form-control bg-light"
            readonly
            value="{{ $editingPayroll
                ? '₹' . number_format(
                    (float)
                    $payroll->total_deductions,
                    2
                )
                : 'Calculated automatically'
            }}"
        >
    </div>

    {{-- TOTAL NET PAYABLE --}}
    <div class="col-md-4">
        <label class="form-label">
            Total Net Payable
        </label>

        <input
            type="text"
            class="form-control bg-light fw-bold"
            readonly
            value="{{ $editingPayroll
                ? '₹' . number_format(
                    (float)
                    $payroll->net_salary,
                    2
                )
                : 'Calculated automatically'
            }}"
        >
    </div>

    <input
        type="hidden"
        name="status"
        value="{{ old(
            'status',
            $editingPayroll
                ? $payroll->status
                : 'Draft'
        ) }}"
    >

</div>