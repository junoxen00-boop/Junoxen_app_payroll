@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-3 col-lg-4">
            @include('admin.payroll-management.partials.nav')
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h1 class="h3 mb-1">Attendance Import</h1>
                    <p class="text-muted mb-0">Upload the monthly biometric attendance workbook, review exceptions, then confirm it for payroll.</p>
                </div>
            </div>

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
                <div class="card-body p-4">
                    <h5 class="fw-bold">Upload Attendance Excel</h5>
                    <p class="text-muted small">Expected sheet: <strong>Attendance Record</strong>. The importer reads the Made Date period and the Employee ID / Name / Department / day columns automatically.</p>

                    <form method="POST" action="{{ route('admin.payroll-management.attendance.store') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-9">
                            <label class="form-label">Attendance Workbook (.xlsx)</label>
                            <input type="file" name="attendance_file" accept=".xlsx" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                                Upload & Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white"><h5 class="fw-bold mb-0">Import History</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th>Employees</th>
                                    <th>Matched</th>
                                    <th>Unmatched</th>
                                    <th>Needs Review</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($imports as $import)
                                    <tr>
                                        <td>{{ $import->original_filename }}</td>
                                        <td>{{ DateTime::createFromFormat('!m', $import->payroll_month)->format('M') }} {{ $import->payroll_year }}</td>
                                        <td><span class="badge {{ $import->status === 'Confirmed' ? 'bg-success' : ($import->status === 'Superseded' ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ $import->status }}</span></td>
                                        <td>{{ $import->row_count }}</td>
                                        <td>{{ $import->matched_count }}</td>
                                        <td>{{ $import->unmatched_count }}</td>
                                        <td>{{ $import->needs_review_count }}</td>
                                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.payroll-management.attendance.show', $import) }}">Review</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-5">No attendance imports yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($imports->hasPages())
                    <div class="card-footer bg-white">{{ $imports->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection