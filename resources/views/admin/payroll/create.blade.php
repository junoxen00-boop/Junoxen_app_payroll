@extends('layouts.admin')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

    <div>
        <h3 class="fw-bold mb-1">
            Generate Payroll
        </h3>

        <p class="text-muted mb-0">
            Select an employee and payroll period,
            then enter the Basic Salary and payroll details.
        </p>
    </div>

</div>

<div class="card">

    <div class="card-body p-4">

        <form
            method="POST"
            action="{{ route('admin.payroll.store') }}"
        >

            @csrf

            <div class="row g-3 mb-4">

                <div class="col-md-6">

                    <label class="form-label">
                        Employee *
                    </label>

                    <select
                        name="employee_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select employee
                        </option>

                        @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    old('employee_id')
                                    == $employee->id
                                )
                            >
                                {{ $employee->employee_id }}
                                -
                                {{ $employee->full_name }}

                                ({{ $employee->department?->name }})
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Month *
                    </label>

                    <select
                        name="payroll_month"
                        class="form-select"
                        required
                    >

                        @for($m = 1; $m <= 12; $m++)

                            <option
                                value="{{ $m }}"
                                @selected(
                                    old(
                                        'payroll_month',
                                        now()->month
                                    ) == $m
                                )
                            >
                                {{
                                    DateTime::createFromFormat(
                                        '!m',
                                        $m
                                    )->format('F')
                                }}
                            </option>

                        @endfor

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Year *
                    </label>

                    <input
                        type="number"
                        name="payroll_year"
                        min="2000"
                        max="2100"
                        class="form-control"
                        value="{{ old(
                            'payroll_year',
                            now()->year
                        ) }}"
                        required
                    >

                </div>

            </div>

            @include(
                'admin.payroll._adjustments'
            )

            @if($errors->any())

                <div class="alert alert-danger mt-3">

                    <ul class="mb-0">

                        @foreach(
                            $errors->all()
                            as $error
                        )

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="alert alert-info mt-4 mb-3">

                <i class="bi bi-shield-check me-2"></i>

                Professional Tax, Provident Fund,
                LOP Deduction, Total Deductions
                and Total Net Payable are calculated
                securely on the server.

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >

                <i class="bi bi-file-earmark-plus me-1"></i>

                Generate Payroll

            </button>

        </form>

    </div>

</div>

@endsection