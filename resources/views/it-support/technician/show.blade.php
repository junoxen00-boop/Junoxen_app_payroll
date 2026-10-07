@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h3 class="fw-bold mb-1">{{ $ticket->ticket_number }}</h3><p class="text-muted mb-0">{{ $ticket->subject }}</p></div><a href="{{ route('it-support.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4"><div class="card-body p-4">
                <div class="row g-3 mb-4"><div class="col-md-4"><small class="text-muted">Employee</small><div>{{ $ticket->employee?->full_name }}</div></div><div class="col-md-4"><small class="text-muted">Department</small><div>{{ $ticket->employee?->department?->name }}</div></div><div class="col-md-4"><small class="text-muted">Priority</small><div>{{ $ticket->priority }}</div></div><div class="col-md-4"><small class="text-muted">Status</small><div>{{ $ticket->status }}</div></div><div class="col-md-4"><small class="text-muted">Category</small><div>{{ $ticket->category }}</div></div><div class="col-md-4"><small class="text-muted">Assigned</small><div>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div></div></div>
                <h5 class="fw-bold">Description</h5><div class="p-3 bg-light rounded-3" style="white-space:pre-wrap">{{ $ticket->description }}</div>
                @if($ticket->resolution)<h5 class="fw-bold mt-4">Resolution</h5><div class="alert alert-success" style="white-space:pre-wrap">{{ $ticket->resolution }}</div>@endif
            </div></div>
            <div class="card"><div class="card-body p-4"><h5 class="fw-bold mb-3">Activity / Replies</h5>@forelse($ticket->comments as $comment)@if(!$comment->is_internal)<div class="border-bottom py-3"><div class="d-flex justify-content-between"><strong>{{ $comment->user?->name ?? 'System' }}</strong><small class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small></div><div class="mt-1" style="white-space:pre-wrap">{{ $comment->message }}</div></div>@endif @empty<p class="text-muted">No activity yet.</p>@endforelse
                <form method="POST" action="{{ route('it-support.comments.store',$ticket) }}" class="mt-4">@csrf<label class="form-label">Reply</label><textarea name="message" class="form-control mb-2" rows="3" required></textarea><button class="btn btn-primary">Add Reply</button></form>
            </div></div>
        </div>
        <div class="col-lg-4">
            @if(!$ticket->assigned_to_user_id)
            <div class="card mb-4"><div class="card-body p-4"><h5 class="fw-bold">Take Ticket</h5><form method="POST" action="{{ route('it-support.assign-self',$ticket) }}">@csrf<button class="btn btn-primary w-100">Assign To Me</button></form></div></div>
            @else
            <div class="card mb-4"><div class="card-body p-4"><h5 class="fw-bold">Update Status</h5><form method="POST" action="{{ route('it-support.status.update',$ticket) }}">@csrf @method('PATCH')<select name="status" class="form-select mb-3"><option value="Assigned" @selected($ticket->status==='Assigned')>Assigned</option><option value="In Progress" @selected($ticket->status==='In Progress')>In Progress</option></select><button class="btn btn-warning w-100">Update Status</button></form></div></div>
            @endif
            @if($ticket->assigned_to_user_id === auth()->id() && !in_array($ticket->status,['Resolved','Closed']))
            <div class="card"><div class="card-body p-4"><h5 class="fw-bold">Resolve Ticket</h5><form method="POST" action="{{ route('it-support.resolve',$ticket) }}">@csrf<textarea name="resolution" class="form-control mb-3" rows="5" required placeholder="Resolution notes"></textarea><button class="btn btn-success w-100">Mark Resolved</button></form></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
