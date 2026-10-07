@extends('layouts.admin')

@section('content')
<div class="container-fluid"><div class="row g-4"><div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div><div class="col-xl-9 col-lg-8">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="alert alert-warning"><strong>Preparation only:</strong> this page does not submit information to the ATO. It creates internal Draft/Ready payroll snapshots for future integration.</div>
<div class="card mb-4"><div class="card-body"><h4 class="fw-bold mb-3">Create STP Preparation Batch</h4><form method="POST" action="{{ route('admin.payroll-management.stp.store') }}" class="row g-3">@csrf
<div class="col-md-4"><label class="form-label">Month</label><select name="payroll_month" class="form-select">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($m==now()->month)>{{ DateTime::createFromFormat('!m',$m)->format('F') }}</option>@endfor</select></div>
<div class="col-md-4"><label class="form-label">Year</label><input type="number" name="payroll_year" class="form-control" value="{{ now()->year }}" min="2000" max="2100"></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option>Draft</option><option>Ready</option></select></div>
<div class="col-12"><button class="btn btn-primary">Save Preparation Batch</button></div></form></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Period</th><th>Employees</th><th>Gross Payments</th><th>Tax Amount</th><th>Status</th><th>Created</th></tr></thead><tbody>@forelse($batches as $b)<tr><td>{{ DateTime::createFromFormat('!m',$b->payroll_month)->format('F') }} {{ $b->payroll_year }}</td><td>{{ $b->employee_count }}</td><td>₹{{ number_format((float)$b->gross_payments,2) }}</td><td>₹{{ number_format((float)$b->tax_amount,2) }}</td><td><span class="badge bg-primary">{{ $b->status }}</span></td><td>{{ $b->created_at?->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-5">No STP preparation batches.</td></tr>@endforelse</tbody></table></div></div>@if($batches->hasPages())<div class="card-footer bg-white">{{ $batches->links('pagination::bootstrap-5') }}</div>@endif</div>
</div></div></div>
@endsection
