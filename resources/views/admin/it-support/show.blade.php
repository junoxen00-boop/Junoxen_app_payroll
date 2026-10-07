@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h3 class="fw-bold mb-1">{{ $ticket->ticket_number }}</h3><p class="text-muted mb-0">{{ $ticket->subject }}</p></div><a href="{{ route('admin.it-support.index') }}" class="btn btn-outline-secondary">All Tickets</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4"><div class="card-body p-4">
                <div class="row g-3 mb-4"><div class="col-md-4"><small class="text-muted">Employee</small><div>{{ $ticket->employee?->full_name }}</div></div><div class="col-md-4"><small class="text-muted">Employee ID</small><div>{{ $ticket->employee?->employee_id }}</div></div><div class="col-md-4"><small class="text-muted">Department</small><div>{{ $ticket->employee?->department?->name }}</div></div><div class="col-md-4"><small class="text-muted">Category</small><div>{{ $ticket->category }}</div></div><div class="col-md-4"><small class="text-muted">Priority</small><div>{{ $ticket->priority }}</div></div><div class="col-md-4"><small class="text-muted">Status</small><div>{{ $ticket->status }}</div></div><div class="col-md-4"><small class="text-muted">Assigned To</small><div>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div></div><div class="col-md-4"><small class="text-muted">Created</small><div>{{ $ticket->created_at->format('d M Y H:i') }}</div></div><div class="col-md-4"><small class="text-muted">Updated</small><div>{{ $ticket->updated_at->format('d M Y H:i') }}</div></div></div>
                <h5 class="fw-bold">Description</h5><div class="p-3 bg-light rounded-3" style="white-space:pre-wrap">{{ $ticket->description }}</div>
                @if($ticket->resolution)<h5 class="fw-bold mt-4">Resolution</h5><div class="alert alert-success mb-0" style="white-space:pre-wrap">{{ $ticket->resolution }}<div class="small mt-2">Resolved by {{ $ticket->resolvedBy?->name ?? 'System' }} @if($ticket->resolved_at) on {{ $ticket->resolved_at->format('d M Y H:i') }} @endif</div></div>@endif
            </div></div>

            <div class="card"><div class="card-body p-4"><h5 class="fw-bold mb-3">Ticket History / Comments</h5>
                @forelse($ticket->comments as $comment)<div class="border-bottom py-3"><div class="d-flex justify-content-between gap-3"><div><strong>{{ $comment->user?->name ?? 'System' }}</strong>@if($comment->is_internal)<span class="badge bg-dark ms-2">Internal</span>@endif</div><small class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small></div><div class="mt-1" style="white-space:pre-wrap">{{ $comment->message }}</div></div>@empty<p class="text-muted">No activity yet.</p>@endforelse
                <form method="POST" action="{{ route('admin.it-support.comments.store',$ticket) }}" class="mt-4">@csrf<label class="form-label">Add Reply / Internal Note</label><textarea name="message" class="form-control mb-2" rows="3" required></textarea><div class="form-check mb-3"><input class="form-check-input" type="checkbox" value="1" name="is_internal" id="is_internal"><label class="form-check-label" for="is_internal">Internal note (hidden from employee)</label></div><button class="btn btn-primary">Add Note</button></form>
            </div></div>
        </div>

        <div class="col-lg-4">
            @if(!in_array($ticket->status,['Resolved','Closed']))
            <div class="card mb-4"><div class="card-body p-4"><h5 class="fw-bold">Assign IT Employee</h5><form method="POST" action="{{ route('admin.it-support.assign',$ticket) }}">@csrf<select name="assigned_to_user_id" class="form-select mb-3" required><option value="">Select IT employee</option>@foreach($itEmployees as $employee)<option value="{{ $employee->user_id }}" @selected($ticket->assigned_to_user_id===$employee->user_id)>{{ $employee->full_name }}</option>@endforeach</select><button class="btn btn-primary w-100">Assign / Reassign</button></form></div></div>

            <div class="card mb-4"><div class="card-body p-4"><h5 class="fw-bold">Priority / Status</h5><form method="POST" action="{{ route('admin.it-support.update',$ticket) }}">@csrf @method('PATCH')<label class="form-label">Priority</label><select name="priority" class="form-select mb-3">@foreach(\App\Models\ItSupportTicket::PRIORITIES as $priority)<option value="{{ $priority }}" @selected($ticket->priority===$priority)>{{ $priority }}</option>@endforeach</select><label class="form-label">Status</label><select name="status" class="form-select mb-3">@foreach(['Open','Assigned','In Progress','Closed'] as $status)<option value="{{ $status }}" @selected($ticket->status===$status)>{{ $status }}</option>@endforeach</select><button class="btn btn-warning w-100">Update Ticket</button></form></div></div>

            <div class="card"><div class="card-body p-4"><h5 class="fw-bold">Resolve Ticket</h5><form method="POST" action="{{ route('admin.it-support.resolve',$ticket) }}">@csrf<textarea name="resolution" class="form-control mb-3" rows="5" required placeholder="Resolution notes"></textarea><button class="btn btn-success w-100">Mark Resolved</button></form></div></div>
            @else
            <div class="card"><div class="card-body p-4"><h5 class="fw-bold">Reopen Ticket</h5><p class="text-muted">Previous resolution details remain preserved.</p><form method="POST" action="{{ route('admin.it-support.reopen',$ticket) }}">@csrf<button class="btn btn-warning w-100">Reopen Ticket</button></form></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
