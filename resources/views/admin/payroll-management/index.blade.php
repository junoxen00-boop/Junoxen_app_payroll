@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            @include('admin.payroll-management.partials.nav')
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="h3 mb-1">Payroll Overview</h1>
                    <p class="text-muted mb-0">Current period: {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payroll.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Generate Payroll</a>
                    <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-primary">Payroll History</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @php
                    $cards = [
                        ['Total Employees', $totalEmployees, 'bi-people'],
                        ['Active Employees', $activeEmployees, 'bi-person-check'],
                        ['Payrolls This Month', $payrollsThisMonth, 'bi-receipt'],
                        ['Draft Payrolls', $draftPayrolls, 'bi-pencil-square'],
                        ['Generated Payrolls', $generatedPayrolls, 'bi-file-earmark-check'],
                        ['Paid Payrolls', $paidPayrolls, 'bi-check-circle'],
                    ];
                @endphp
                @foreach($cards as [$label, $value, $icon])
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary-subtle text-primary p-3"><i class="bi {{ $icon }} fs-4"></i></div>
                            <div><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-bold">{{ number_format($value) }}</div></div>
                        </div></div>
                    </div>
                @endforeach
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Gross Payroll</div><div class="fs-4 fw-bold">₹{{ number_format((float) $grossPayroll, 2) }}</div></div></div></div>
                <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Total Deductions</div><div class="fs-4 fw-bold">₹{{ number_format((float) $totalDeductions, 2) }}</div></div></div></div>
                <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Total Net Payable</div><div class="fs-4 fw-bold text-success">₹{{ number_format((float) $totalNetPayable, 2) }}</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="card h-100"><div class="card-body">
                        <h5 class="fw-bold mb-3">Recent Payrolls</h5>
                        <div class="table-responsive"><table class="table table-sm align-middle">
                            <thead><tr><th>Payroll</th><th>Employee</th><th class="text-end">Net</th></tr></thead>
                            <tbody>
                            @forelse($recentPayrolls as $payroll)
                                <tr><td><a href="{{ route('admin.payroll.show', $payroll) }}">{{ $payroll->payroll_number }}</a></td><td>{{ $payroll->employee?->full_name }}</td><td class="text-end">₹{{ number_format((float) $payroll->net_salary, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No payroll records yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table></div>
                    </div></div>
                </div>
                <div class="col-xl-6">
                    <div class="card h-100"><div class="card-body">
                        <h5 class="fw-bold mb-3">Pending Payrolls</h5>
                        <div class="table-responsive"><table class="table table-sm align-middle">
                            <thead><tr><th>Employee</th><th>Status</th><th class="text-end">Net</th></tr></thead>
                            <tbody>
                            @forelse($pendingPayrolls as $payroll)
                                <tr><td>{{ $payroll->employee?->full_name }}</td><td><span class="badge bg-primary">{{ $payroll->status }}</span></td><td class="text-end">₹{{ number_format((float) $payroll->net_salary, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No pending payrolls.</td></tr>
                            @endforelse
                            </tbody>
                        </table></div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
