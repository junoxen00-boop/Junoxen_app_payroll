@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Monthly Payroll</h3>
    <p class="text-muted mb-0">Generate payroll for all active employees with valid salary structures. Existing employee/month records are skipped.</p>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.payroll.bulk.store') }}" onsubmit="return confirm('Generate monthly payroll for all eligible active employees?');">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="payroll_month" class="form-select" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(now()->month == $m)>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <input type="number" name="payroll_year" class="form-control" min="2000" max="2100" value="{{ now()->year }}" required>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Generate Eligible Payrolls</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Active Employees Preview</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Salary Structure</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        @php
                            $hasSalaryStructure = $employee->salaryStructures
                                ->where('status', 'Active')
                                ->count() > 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $employee->full_name }}</div>
                                <div class="text-muted small">{{ $employee->employee_id }}</div>
                            </td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td>
                                @if($hasSalaryStructure)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <div class="d-flex flex-column align-items-start gap-2">
                                        <span class="badge bg-warning text-dark">Missing</span>
                                        <a href="{{ route('admin.salary-structures.create', ['employee_id' => $employee->id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Create Salary Structure
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mb-0 mt-3">
            Bulk payroll uses the same server-side PF, Professional Tax, LOP and net-pay calculation service as single payroll generation.
        </div>
    </div>
</div>
@endsection
