@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div>
        <div class="col-xl-9 col-lg-8">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <div class="card mb-4"><div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3">
                <div><h1 class="h3 mb-1">{{ $employee->full_name }}</h1><div class="text-muted">{{ $employee->employee_id }} · {{ $employee->designation }} · {{ $employee->department?->name ?? 'No Department' }}</div></div>
                <span class="badge align-self-start {{ $employee->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $employee->status }}</span>
            </div></div>

            @php($profile = $employee->payrollProfile)
            <form method="POST" action="{{ route('admin.payroll-management.employees.update', $employee) }}">
                @csrf @method('PUT')
                <div class="card mb-4"><div class="card-body">
                    <h5 class="fw-bold mb-3">Employee Information</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Employee ID</label><input class="form-control" value="{{ $employee->employee_id }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" value="{{ $employee->email }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Mobile</label><input class="form-control" value="{{ $employee->mobile_number }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Department</label><input class="form-control" value="{{ $employee->department?->name }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Designation</label><input class="form-control" value="{{ $employee->designation }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Joining Date</label><input class="form-control" value="{{ $employee->joining_date?->format('d M Y') }}" readonly></div>
                    </div>
                </div></div>

                <div class="card mb-4"><div class="card-body">
                    <h5 class="fw-bold mb-3">Employment & Payroll Information</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Employment Type</label><input name="employment_type" class="form-control" value="{{ old('employment_type', $profile?->employment_type) }}" placeholder="Full-time / Part-time / Contract"></div>
                        <div class="col-md-4"><label class="form-label">Payroll Status</label><select name="payroll_status" class="form-select">@foreach(['Active','Inactive','On Hold'] as $v)<option value="{{ $v }}" @selected(old('payroll_status', $profile?->payroll_status ?? 'Active') === $v)>{{ $v }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">Pay Frequency</label><select name="pay_frequency" class="form-select">@foreach(['Weekly','Fortnightly','Monthly'] as $v)<option value="{{ $v }}" @selected(old('pay_frequency', $profile?->pay_frequency ?? 'Monthly') === $v)>{{ $v }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $profile?->start_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $profile?->end_date?->format('Y-m-d')) }}"></div>
                        <div class="col-md-3"><label class="form-label">Default Basic Salary</label><input type="number" min="0" step="0.01" name="default_basic_salary" class="form-control" value="{{ old('default_basic_salary', $profile?->default_basic_salary ?? 0) }}"></div>
                        <div class="col-md-3"><label class="form-label">Default Bonus</label><input type="number" min="0" step="0.01" name="default_bonus" class="form-control" value="{{ old('default_bonus', $profile?->default_bonus ?? 0) }}"></div>
                        <div class="col-12"><label class="form-label">Payroll Notes</label><textarea name="payroll_notes" class="form-control" rows="3">{{ old('payroll_notes', $profile?->payroll_notes) }}</textarea></div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">These defaults do not replace the existing monthly Generate Payroll calculations.</div>
                </div></div>

                <div class="card mb-4"><div class="card-body">
                    <h5 class="fw-bold mb-3">Bank Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Account Holder Name</label><input name="account_holder_name" class="form-control" value="{{ old('account_holder_name', $profile?->account_holder_name) }}"></div>
                        <div class="col-md-6"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control" value="{{ old('bank_name', $profile?->bank_name) }}"></div>
                        <div class="col-md-6"><label class="form-label">Account Number</label><input name="account_number" class="form-control" value="{{ old('account_number', $profile?->account_number) }}"></div>
                        <div class="col-md-6"><label class="form-label">IFSC / Routing Identifier</label><input name="routing_identifier" class="form-control" value="{{ old('routing_identifier', $profile?->routing_identifier) }}"></div>
                        <div class="col-md-6"><label class="form-label">Branch</label><input name="bank_branch" class="form-control" value="{{ old('bank_branch', $profile?->bank_branch) }}"></div>
                    </div>
                    <div class="small text-muted mt-2">Bank fields are encrypted by Laravel before storage.</div>
                </div></div>

                <div class="d-flex justify-content-end mb-4"><button class="btn btn-primary">Save Employee Payroll Card</button></div>
            </form>

            <div class="card"><div class="card-body">
                <h5 class="fw-bold mb-3">Payroll History</h5>
                <div class="table-responsive"><table class="table align-middle">
                    <thead><tr><th>Payroll Number</th><th>Period</th><th>Gross</th><th>Deductions</th><th>Net Payable</th><th>Status</th></tr></thead>
                    <tbody>@forelse($employee->payrolls as $p)<tr><td><a href="{{ route('admin.payroll.show', $p) }}">{{ $p->payroll_number }}</a></td><td>{{ DateTime::createFromFormat('!m', $p->payroll_month)->format('M') }} {{ $p->payroll_year }}</td><td>₹{{ number_format((float)$p->gross_salary,2) }}</td><td>₹{{ number_format((float)$p->total_deductions,2) }}</td><td>₹{{ number_format((float)$p->net_salary,2) }}</td><td><span class="badge bg-primary">{{ $p->status }}</span></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No payroll history.</td></tr>@endforelse</tbody>
                </table></div>
            </div></div>
        </div>
    </div>
</div>
@endsection
