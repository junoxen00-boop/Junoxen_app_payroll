@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">IT Support</h3>
            <p class="text-muted mb-0">Raise and track your IT support requests.</p>
        </div>
        <a href="{{ route('employee.it-support.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Raise New Ticket
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-lg-4"><label class="form-label">Search</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Ticket number or subject"></div>
                <div class="col-lg-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::STATUSES as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></div>
                <div class="col-lg-2"><label class="form-label">Priority</label><select name="priority" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::PRIORITIES as $priority)<option value="{{ $priority }}" @selected(request('priority')===$priority)>{{ $priority }}</option>@endforeach</select></div>
                <div class="col-lg-2"><label class="form-label">Category</label><select name="category" class="form-select"><option value="">All</option>@foreach(\App\Models\ItSupportTicket::CATEGORIES as $category)<option value="{{ $category }}" @selected(request('category')===$category)>{{ $category }}</option>@endforeach</select></div>
                <div class="col-lg-2 d-flex align-items-end gap-2"><button class="btn btn-primary w-100">Filter</button><a href="{{ route('employee.it-support.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Ticket</th><th>Subject</th><th>Category</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th><th></th></tr></thead>
                    <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->category }}</td>
                            <td><span class="badge {{ $ticket->priority === 'Critical' ? 'bg-danger' : ($ticket->priority === 'High' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $ticket->priority }}</span></td>
                            <td><span class="badge {{ $ticket->status === 'Resolved' ? 'bg-success' : ($ticket->status === 'In Progress' ? 'bg-warning text-dark' : 'bg-primary') }}">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
                            <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end"><a href="{{ route('employee.it-support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No IT support tickets found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())<div class="card-footer bg-white">{{ $tickets->links('pagination::bootstrap-5') }}</div>@endif
    </div>
</div>
@endsection
