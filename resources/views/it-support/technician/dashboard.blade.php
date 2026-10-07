@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="fw-bold mb-1">IT Support Dashboard</h3><p class="text-muted mb-0">Manage open and assigned IT support tickets.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('it-support.open') }}" class="btn btn-outline-primary">Open Tickets</a><a href="{{ route('it-support.assigned') }}" class="btn btn-primary">Assigned To Me</a></div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Open Tickets',$openCount,'bi-inbox','primary'],['Assigned To Me',$assignedCount,'bi-person-check','secondary'],['In Progress',$inProgressCount,'bi-arrow-repeat','warning'],['Critical',$criticalCount,'bi-exclamation-triangle','danger'],['Resolved Today',$resolvedTodayCount,'bi-check-circle','success'],['Total Resolved',$resolvedTotalCount,'bi-archive','success']
        ] as [$label,$value,$icon,$color])
        <div class="col-xl-2 col-md-4 col-6"><div class="card h-100"><div class="card-body"><small class="text-muted">{{ $label }}</small><div class="d-flex justify-content-between align-items-center mt-2"><h3 class="fw-bold mb-0">{{ $value }}</h3><i class="bi {{ $icon }} text-{{ $color }} fs-3"></i></div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6"><div class="card h-100"><div class="card-header bg-white"><strong>Unassigned Tickets</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Ticket</th><th>Subject</th><th>Priority</th><th></th></tr></thead><tbody>@forelse($unassignedTickets as $ticket)<tr><td>{{ $ticket->ticket_number }}</td><td>{{ $ticket->subject }}</td><td>{{ $ticket->priority }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('it-support.show',$ticket) }}">View</a></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No unassigned tickets.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-lg-6"><div class="card h-100"><div class="card-header bg-white"><strong>My Active Tickets</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Ticket</th><th>Subject</th><th>Status</th><th></th></tr></thead><tbody>@forelse($myTickets as $ticket)<tr><td>{{ $ticket->ticket_number }}</td><td>{{ $ticket->subject }}</td><td>{{ $ticket->status }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('it-support.show',$ticket) }}">View</a></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No active assigned tickets.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>
</div>
@endsection
