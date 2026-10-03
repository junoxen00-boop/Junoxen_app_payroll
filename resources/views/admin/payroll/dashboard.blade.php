@extends('layouts.admin')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h3 class="fw-bold mb-1">Payroll Dashboard</h3><p class="text-muted mb-0">Junoxen PVT LTD · {{ now()->format('F Y') }}</p></div><div class="d-flex gap-2"><a href="{{ route('admin.payroll.create') }}" class="btn btn-primary"><i class="bi bi-file-earmark-plus me-1"></i> Generate Payroll</a><a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-primary">All Payrolls</a></div></div>
@php
$cards = [
    [
        'Active Employees',
        $totalEmployees,
        'bi-people'
    ],

    [
        'Generated This Month',
        $generatedThisMonth,
        'bi-file-earmark-text'
    ],

    [
        'Paid Payrolls',
        $paidCount,
        'bi-check-circle'
    ],

    [
        'Pending Payrolls',
        $pendingCount,
        'bi-hourglass-split'
    ],
];
@endphp
<div class="row g-3 mb-4">@foreach($cards as [$label,$value,$icon])<div class="col-6 col-lg"><div class="card h-100"><div class="card-body"><i class="bi {{ $icon }} fs-4 text-primary"></i><div class="text-muted small mt-2">{{ $label }}</div><div class="fs-3 fw-bold">{{ $value }}</div></div></div></div>@endforeach</div>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Gross Payroll</div><div class="fs-4 fw-bold">₹{{ number_format((float)$grossTotal,2) }}</div></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Total Deductions</div><div class="fs-4 fw-bold">₹{{ number_format((float)$deductionsTotal,2) }}</div></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Net Payroll</div><div class="fs-4 fw-bold text-success">₹{{ number_format((float)$netTotal,2) }}</div></div></div></div></div>
<div class="card"><div class="card-header bg-white border-0 p-4 pb-2"><h5 class="fw-bold mb-0">Recent Payrolls</h5></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Payroll #</th><th>Employee</th><th>Period</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th><th></th></tr></thead><tbody>@forelse($recentPayrolls as $p)<tr><td>{{ $p->payroll_number }}</td><td>{{ $p->employee->full_name }}<div class="small text-muted">{{ $p->employee->employee_id }}</div></td><td>{{ DateTime::createFromFormat('!m',$p->payroll_month)->format('M') }} {{ $p->payroll_year }}</td><td>₹{{ number_format((float)$p->gross_salary,2) }}</td><td>₹{{ number_format((float)$p->total_deductions,2) }}</td><td class="fw-bold">₹{{ number_format((float)$p->net_salary,2) }}</td><td><span class="badge {{ $p->status==='Paid'?'bg-success':($p->status==='Cancelled'?'bg-danger':'bg-primary') }}">{{ $p->status }}</span></td><td><a href="{{ route('admin.payroll.show',$p) }}" class="btn btn-sm btn-outline-primary">View</a></td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-5">No payroll generated yet.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
