@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            @include('admin.payroll-management.partials.nav')
        </div>

        <div class="col-xl-9 col-lg-8">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-1">Payroll Settings</h1>
                    <p class="text-muted mb-4">Administrative payroll defaults. Attendance deductions remain configurable and are calculated server-side.</p>

                    <form method="POST" action="{{ route('admin.payroll-management.settings.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input name="company_name" class="form-control" value="{{ old('company_name', $settings->company_name) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Pay Frequency</label>
                            <select name="pay_frequency" class="form-select">
                                @foreach(['Weekly','Fortnightly','Monthly'] as $value)
                                    <option value="{{ $value }}" @selected(old('pay_frequency', $settings->pay_frequency) === $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Currency</label>
                            <input name="currency" class="form-control" value="{{ old('currency', $settings->currency) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Default Payment Method</label>
                            <select name="default_payment_method" class="form-select">
                                @foreach(['Bank Transfer','Cash','Cheque','Other'] as $value)
                                    <option value="{{ $value }}" @selected(old('default_payment_method', $settings->default_payment_method) === $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">LOP Calculation Basis</label>
                            <select name="lop_calculation_basis" class="form-select">
                                <option value="Calendar Days" selected>Calendar Days</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payroll Year</label>
                            <input type="number" name="payroll_year" min="2000" max="2100" class="form-control" value="{{ old('payroll_year', $settings->payroll_year) }}">
                        </div>

                        <div class="col-12"><hr></div>
                        <div class="col-12">
                            <h5 class="fw-bold mb-1">Attendance Payroll Policy</h5>
                            <p class="text-muted small mb-0">Enable this only after the shift and grace rules are confirmed. The system deducts late-arrival minutes only; early-exit and missing-hours deductions remain disabled.</p>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="attendance_payroll_enabled" value="0">
                                <input class="form-check-input" type="checkbox" name="attendance_payroll_enabled" value="1" id="attendancePayrollEnabled" @checked(old('attendance_payroll_enabled', $settings->attendance_payroll_enabled))>
                                <label class="form-check-label" for="attendancePayrollEnabled">Attendance Payroll Enabled</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Default Shift Start</label>
                            <input type="time" name="default_shift_start" class="form-control" value="{{ old('default_shift_start', $settings->default_shift_start ? substr($settings->default_shift_start, 0, 5) : '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Default Shift End</label>
                            <input type="time" name="default_shift_end" class="form-control" value="{{ old('default_shift_end', $settings->default_shift_end ? substr($settings->default_shift_end, 0, 5) : '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Grace Minutes</label>
                            <input type="number" name="grace_minutes" min="0" max="240" class="form-control" value="{{ old('grace_minutes', $settings->grace_minutes ?? 10) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Grace Deduction Mode</label>
                            <select name="grace_deduction_mode" class="form-select">
                                <option value="excess" @selected(old('grace_deduction_mode', $settings->grace_deduction_mode ?? 'excess') === 'excess')>Deduct only minutes after grace</option>
                                <option value="full" @selected(old('grace_deduction_mode', $settings->grace_deduction_mode ?? 'excess') === 'full')>Deduct all late minutes once grace exceeded</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Standard Work Minutes / Day</label>
                            <input type="number" name="standard_work_minutes_per_day" min="60" max="1440" class="form-control" value="{{ old('standard_work_minutes_per_day', $settings->standard_work_minutes_per_day ?? 480) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Late Deduction Rounding</label>
                            <select name="late_deduction_rounding" class="form-select">
                                @foreach(['Exact Minutes','Nearest 15 Minutes','Nearest 30 Minutes','Whole Hour'] as $value)
                                    <option value="{{ $value }}" @selected(old('late_deduction_rounding', $settings->late_deduction_rounding ?? 'Exact Minutes') === $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="deduct_late_arrival" value="0">
                                <input class="form-check-input" type="checkbox" name="deduct_late_arrival" value="1" id="deductLateArrival" @checked(old('deduct_late_arrival', $settings->deduct_late_arrival ?? true))>
                                <label class="form-check-label" for="deductLateArrival">Deduct Late Arrival</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Payslip Footer</label>
                            <textarea name="payslip_footer" class="form-control" rows="3">{{ old('payslip_footer', $settings->payslip_footer) }}</textarea>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary">Save Payroll Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection