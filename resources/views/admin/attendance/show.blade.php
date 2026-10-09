@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            @include('admin.payroll-management.partials.nav')
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 class="h3 mb-1">Attendance Import Review</h1>
                    <div class="text-muted">{{ $import->original_filename }} · {{ DateTime::createFromFormat('!m', $import->payroll_month)->format('F') }} {{ $import->payroll_year }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payroll-management.attendance.index') }}" class="btn btn-outline-secondary">Back</a>
                    @if($import->status !== 'Confirmed')
                        <form method="POST" action="{{ route('admin.payroll-management.attendance.confirm', $import) }}">
                            @csrf
                            <button class="btn btn-success" onclick="return confirm('Confirm this attendance import for payroll? Existing confirmed attendance for this month will be superseded.')">Confirm Import</button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Employees</div><div class="fs-3 fw-bold">{{ $import->row_count }}</div></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Matched</div><div class="fs-3 fw-bold text-success">{{ $import->matched_count }}</div></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Unmatched</div><div class="fs-3 fw-bold text-danger">{{ $import->unmatched_count }}</div></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Needs Review</div><div class="fs-3 fw-bold text-warning">{{ $import->needs_review_count }}</div></div></div></div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white"><h5 class="fw-bold mb-0">Employee Summary</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Source Employee</th><th>Match</th><th>Days</th><th>Present</th><th>Late Minutes</th><th>Deductible Minutes</th><th>Review</th></tr></thead>
                            <tbody>
                                @foreach($employeeSummaries as $summary)
                                    <tr>
                                        <td>{{ $summary->source_employee_id }} · {{ $summary->source_employee_name }}</td>
                                        <td><span class="badge {{ $summary->employee_id ? 'bg-success' : 'bg-danger' }}">{{ $summary->match_method }}</span></td>
                                        <td>{{ $summary->days_imported }}</td>
                                        <td>{{ $summary->present_days }}</td>
                                        <td>{{ $summary->late_minutes }}</td>
                                        <td>{{ $summary->deductible_minutes }}</td>
                                        <td><span class="badge {{ (int)$summary->needs_review === 0 ? 'bg-success' : 'bg-warning text-dark' }}">{{ (int)$summary->needs_review === 0 ? 'Ready' : $summary->needs_review . ' record(s)' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Daily Records</h5>
                    <form method="GET" class="d-flex gap-2">
                        <select name="review_status" class="form-select form-select-sm">
                            <option value="">All records</option>
                            <option value="Needs Review" @selected(request('review_status') === 'Needs Review')>Needs Review</option>
                            <option value="Ready" @selected(request('review_status') === 'Ready')>Ready</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary">Filter</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Date</th><th>Employee</th><th>First</th><th>Last</th><th>Late</th><th>Deductible</th><th>Status</th><th>Review</th><th style="min-width:340px">Admin Review</th></tr></thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td>{{ $record->attendance_date->format('d M Y') }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $record->employee?->full_name ?? $record->source_employee_name }}</div>
                                            <small class="text-muted">Source ID: {{ $record->source_employee_id }}</small>
                                        </td>
                                        <td>{{ $record->first_entry ? substr($record->first_entry, 0, 5) : '—' }}</td>
                                        <td>{{ $record->last_exit ? substr($record->last_exit, 0, 5) : '—' }}</td>
                                        <td>{{ $record->late_minutes }} min</td>
                                        <td>{{ $record->deductible_minutes }} min</td>
                                        <td>{{ $record->attendance_status }}</td>
                                        <td>
                                            <span class="badge {{ $record->review_status === 'Ready' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $record->review_status }}</span>
                                            @if($record->review_note)<div class="small text-muted mt-1">{{ $record->review_note }}</div>@endif
                                        </td>
                                        <td>
                                            @if($record->review_status === 'Needs Review')
                                                <form method="POST" action="{{ route('admin.payroll-management.attendance.records.update', $record) }}" class="row g-2">
                                                    @csrf
                                                    @method('PUT')
                                                    @if(!$record->employee_id)
                                                        <div class="col-12"><select name="employee_id" class="form-select form-select-sm" required><option value="">Match employee...</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ $employee->full_name }}</option>@endforeach</select></div>
                                                    @endif
                                                    <div class="col-4"><input type="time" name="first_entry" class="form-control form-control-sm" value="{{ $record->first_entry ? substr($record->first_entry,0,5) : '' }}"></div>
                                                    <div class="col-4"><input type="time" name="last_exit" class="form-control form-control-sm" value="{{ $record->last_exit ? substr($record->last_exit,0,5) : '' }}"></div>
                                                    <div class="col-4"><select name="attendance_status" class="form-select form-select-sm">@foreach(['Present','Paid Leave','Unpaid Leave','Weekly Off','Holiday','Absent','Attendance Missing'] as $status)<option value="{{ $status }}" @selected($record->attendance_status === $status)>{{ $status }}</option>@endforeach</select></div>
                                                    <div class="col-9"><input name="override_reason" class="form-control form-control-sm" placeholder="Required override reason" required></div>
                                                    <div class="col-3"><button class="btn btn-sm btn-primary w-100">Approve</button></div>
                                                </form>
                                            @else
                                                <span class="text-muted small">{{ $record->override_reason ?: 'No action required' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center text-muted py-5">No attendance records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($records->hasPages())<div class="card-footer bg-white">{{ $records->links('pagination::bootstrap-5') }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection