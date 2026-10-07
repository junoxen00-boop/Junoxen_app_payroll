@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="fw-bold mb-1">IT Support</h3><p class="text-muted mb-0">Admin view of all IT support tickets.</p></div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Tickets',$totalCount,'bi-ticket-perforated','primary'],['Open',$openCount,'bi-inbox','info'],['Assigned',$assignedCount,'bi-person-check','secondary'],['In Progress',$inProgressCount,'bi-arrow-repeat','warning'],['Resolved',$resolvedCount,'bi-check-circle','success'],['Critical',$criticalCount,'bi-exclamation-triangle','danger']
        ] as [$label,$value,$icon,$color])
        <div class="col-xl-2 col-md-4 col-6"><div class="card h-100"><div class="card-body"><small class="text-muted">{{ $label }}</small><div class="d-flex justify-content-between align-items-center mt-2"><h3 class="fw-bold mb-0">{{ $value }}</h3><i class="bi {{ $icon }} text-{{ $color }} fs-3"></i></div></div></div></div>
        @endforeach
    </div>

    <div class="card mb-4"><div class="card-body"><form method="GET" class="row g-3">
        <div class="col-lg-3"><label class="form-label">Search</label><input name="search" class="form-control" value="{{ request('search') }}" placeholder="Ticket, subject or employee"></div>
        <div class="col-lg-2"><label class="form-label">Employee</label><select name="employee_id" class="form-select"><option value="">All</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((string)request('employee_id')===(string)$employee->id)>{{ $employee->full_name }}</option>@endforeach</select></div>
        <div class="col-lg-2"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">All</option>@foreach($departments as $department)<option value="{{ $department->id }}" @selected((string)request('department_id')===(string)$department->id)>{{ $department->name }}</option>@endforeach</select></div>
        <div class="col-lg-2"><label class="form-label">Assigned IT</label><select name="assigned_to_user_id" class="form-select"><option value="">All</option>@foreach($itEmployees as $employee)<option value="{{ $employee->user_id }}" @selected((string)request('assigned_to_user_id')===(string)$employee->user_id)>{{ $employee->full_name }}</option>@endforeach</select></div>
        <div class="col-lg-1"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="{{ request('date') }}"></div>
        <div class="col-lg-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::STATUSES as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-lg-2"><label class="form-label">Priority</label><select name="priority" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::PRIORITIES as $priority)<option value="{{ $priority }}" @selected(request('priority')===$priority)>{{ $priority }}</option>@endforeach</select></div>
        <div class="col-lg-3"><label class="form-label">Category</label><select name="category" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::CATEGORIES as $category)<option value="{{ $category }}" @selected(request('category')===$category)>{{ $category }}</option>@endforeach</select></div>
        <div class="col-lg-3 d-flex align-items-end gap-2"><button class="btn btn-primary">Apply Filters</button><a href="{{ route('admin.it-support.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form></div></div>

    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Ticket</th><th>Employee</th><th>Department</th><th>Subject</th><th>Category</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th><th></th></tr></thead><tbody>
        @forelse($tickets as $ticket)<tr><td class="fw-semibold">{{ $ticket->ticket_number }}</td><td>{{ $ticket->employee?->full_name }}</td><td>{{ $ticket->employee?->department?->name }}</td><td>{{ $ticket->subject }}</td><td>{{ $ticket->category }}</td><td><span class="badge {{ $ticket->priority==='Critical'?'bg-danger':($ticket->priority==='High'?'bg-warning text-dark':'bg-secondary') }}">{{ $ticket->priority }}</span></td><td><span class="badge {{ $ticket->status==='Resolved'?'bg-success':($ticket->status==='In Progress'?'bg-warning text-dark':'bg-primary') }}">{{ $ticket->status }}</span></td><td>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td><td>{{ $ticket->created_at->format('d M Y H:i') }}</td><td class="text-end"><a href="{{ route('admin.it-support.show',$ticket) }}" class="btn btn-sm btn-outline-primary">View</a></td></tr>@empty<tr><td colspan="10" class="text-center text-muted py-5">No tickets found.</td></tr>@endforelse
    </tbody></table></div></div>@if($tickets->hasPages())<div class="card-footer bg-white">{{ $tickets->links('pagination::bootstrap-5') }}</div>@endif</div>
</div>
@endsection
