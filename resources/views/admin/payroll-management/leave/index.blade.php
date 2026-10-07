@extends('layouts.admin')

@section('content')
<div class="container-fluid"><div class="row g-4"><div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div><div class="col-xl-9 col-lg-8">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card mb-4"><div class="card-body"><h4 class="fw-bold mb-3">Add Leave</h4><form method="POST" action="{{ route('admin.payroll-management.leave.store') }}" class="row g-3">@csrf
<div class="col-md-6"><label class="form-label">Employee</label><select name="employee_id" class="form-select" required><option value="">Select employee</option>@foreach($employees as $e)<option value="{{ $e->id }}" @selected(old('employee_id')==$e->id)>{{ $e->employee_id }} - {{ $e->full_name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Leave Type</label><input name="leave_type" class="form-control" value="{{ old('leave_type') }}" required></div>
<div class="col-md-3"><label class="form-label">Leave Days</label><input type="number" name="leave_days" min="0.5" max="31" step="0.5" class="form-control" value="{{ old('leave_days', '0.50') }}" required></div>
<div class="col-md-3"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required></div>
<div class="col-md-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required></div>
<div class="col-md-3"><label class="form-label">Paid / Unpaid</label><select name="is_paid" class="form-select"><option value="1">Paid</option><option value="0">Unpaid</option></select></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['Pending','Approved','Rejected'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea></div>
<div class="col-12"><button class="btn btn-primary">Add Leave</button></div></form></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Employee</th><th>Type</th><th>Dates</th><th>Days</th><th>Paid?</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($leaves as $leave)<tr><td>{{ $leave->employee?->full_name }}</td><td>{{ $leave->leave_type }}</td><td>{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</td><td>{{ number_format((float)$leave->leave_days,2) }}</td><td>{{ $leave->is_paid ? 'Paid' : 'Unpaid' }}</td><td><span class="badge bg-primary">{{ $leave->status }}</span></td><td class="text-end"><form method="POST" action="{{ route('admin.payroll-management.leave.destroy',$leave) }}" onsubmit="return confirm('Delete this leave record?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-5">No leave records.</td></tr>@endforelse
</tbody></table></div></div>@if($leaves->hasPages())<div class="card-footer bg-white">{{ $leaves->links('pagination::bootstrap-5') }}</div>@endif</div>
</div></div></div>
@endsection
