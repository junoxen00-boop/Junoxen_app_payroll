@extends('layouts.admin')

@section('content')
<div class="container-fluid"><div class="row g-4"><div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div><div class="col-xl-9 col-lg-8">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card mb-4"><div class="card-body"><h4 class="fw-bold mb-3">Add / Update Timesheet</h4><form method="POST" action="{{ route('admin.payroll-management.timesheets.store') }}" class="row g-3">@csrf
<div class="col-md-6"><label class="form-label">Employee</label><select name="employee_id" class="form-select" required><option value="">Select employee</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->employee_id }} - {{ $e->full_name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Work Date</label><input type="date" name="work_date" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['Draft','Submitted','Approved','Rejected'] as $v)<option>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Start Time</label><input type="time" name="start_time" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">End Time</label><input type="time" name="end_time" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Break Minutes</label><input type="number" name="break_minutes" min="0" class="form-control" value="0"></div>
<div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100">Save Timesheet</button></div>
<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
</form><div class="small text-muted mt-2">Timesheets are attendance/work records only and do not alter salary calculations.</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Employee</th><th>Date</th><th>Start</th><th>End</th><th>Break</th><th>Hours</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($timesheets as $t)<tr><td>{{ $t->employee?->full_name }}</td><td>{{ $t->work_date->format('d M Y') }}</td><td>{{ $t->start_time }}</td><td>{{ $t->end_time }}</td><td>{{ $t->break_minutes }} min</td><td>{{ number_format((float)$t->hours_worked,2) }}</td><td><span class="badge bg-primary">{{ $t->status }}</span></td><td class="text-end"><form method="POST" action="{{ route('admin.payroll-management.timesheets.destroy',$t) }}" onsubmit="return confirm('Delete this timesheet?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-5">No timesheets.</td></tr>@endforelse
</tbody></table></div></div>@if($timesheets->hasPages())<div class="card-footer bg-white">{{ $timesheets->links('pagination::bootstrap-5') }}</div>@endif</div>
</div></div></div>
@endsection
