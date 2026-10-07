@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div>
        <div class="col-xl-9 col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Employees</h1><p class="text-muted mb-0">Payroll profile and employee card management.</p></div></div>

            <div class="card mb-4"><div class="card-body">
                <form method="GET" class="row g-2"><div class="col-md-10"><input class="form-control" name="q" value="{{ $search }}" placeholder="Search employee ID, name, email, designation or department"></div><div class="col-md-2 d-grid"><button class="btn btn-outline-primary">Search</button></div></form>
            </div></div>

            <div class="card"><div class="card-body p-0"><div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Employee</th><th>Department</th><th>Designation</th><th>Status</th><th>Payroll Status</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td><div class="fw-semibold">{{ $employee->full_name }}</div><small class="text-muted">{{ $employee->employee_id }} · {{ $employee->email }}</small></td>
                            <td>{{ $employee->department?->name ?? '—' }}</td>
                            <td>{{ $employee->designation }}</td>
                            <td><span class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $employee->status }}</span></td>
                            <td>{{ $employee->payrollProfile?->payroll_status ?? 'Not configured' }}</td>
                            <td class="text-end"><a href="{{ route('admin.payroll-management.employees.show', $employee) }}" class="btn btn-sm btn-primary">Payroll Card</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No employees found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div></div>
            @if($employees->hasPages())<div class="card-footer bg-white">{{ $employees->links('pagination::bootstrap-5') }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection
