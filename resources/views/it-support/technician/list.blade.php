@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="fw-bold mb-1">{{ $heading }}</h3><p class="text-muted mb-0">IT support work queue.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('it-support.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a><a href="{{ route('it-support.open') }}" class="btn btn-outline-primary">Open</a><a href="{{ route('it-support.assigned') }}" class="btn btn-outline-primary">Assigned</a><a href="{{ route('it-support.resolved') }}" class="btn btn-outline-primary">Resolved</a></div>
    </div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Ticket</th><th>Employee</th><th>Department</th><th>Subject</th><th>Priority</th><th>Status</th><th>Created</th><th></th></tr></thead><tbody>@forelse($tickets as $ticket)<tr><td class="fw-semibold">{{ $ticket->ticket_number }}</td><td>{{ $ticket->employee?->full_name }}</td><td>{{ $ticket->employee?->department?->name }}</td><td>{{ $ticket->subject }}</td><td><span class="badge {{ $ticket->priority==='Critical'?'bg-danger':'bg-secondary' }}">{{ $ticket->priority }}</span></td><td>{{ $ticket->status }}</td><td>{{ $ticket->created_at->format('d M Y H:i') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('it-support.show',$ticket) }}">View</a></td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-5">No tickets found.</td></tr>@endforelse</tbody></table></div></div>@if($tickets->hasPages())<div class="card-footer bg-white">{{ $tickets->links('pagination::bootstrap-5') }}</div>@endif</div>
</div>
@endsection
