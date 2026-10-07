@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="fw-bold mb-1">{{ $ticket->ticket_number }}</h3><p class="text-muted mb-0">{{ $ticket->subject }}</p></div>
        <a href="{{ route('employee.it-support.index') }}" class="btn btn-outline-secondary">Back to My Tickets</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4"><div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><small class="text-muted">Status</small><div><span class="badge bg-primary">{{ $ticket->status }}</span></div></div>
                    <div class="col-md-4"><small class="text-muted">Priority</small><div>{{ $ticket->priority }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Category</small><div>{{ $ticket->category }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Assigned To</small><div>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Created</small><div>{{ $ticket->created_at->format('d M Y H:i') }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Department</small><div>{{ $ticket->department?->name }}</div></div>
                </div>
                <h5 class="fw-bold">Description</h5><div class="p-3 bg-light rounded-3" style="white-space:pre-wrap">{{ $ticket->description }}</div>
                @if($ticket->resolution)
                    <h5 class="fw-bold mt-4">Resolution</h5><div class="alert alert-success mb-0" style="white-space:pre-wrap">{{ $ticket->resolution }}<div class="small mt-2">Resolved by {{ $ticket->resolvedBy?->name ?? 'System' }} @if($ticket->resolved_at) on {{ $ticket->resolved_at->format('d M Y H:i') }} @endif</div></div>
                @endif
            </div></div>

            <div class="card"><div class="card-body p-4">
                <h5 class="fw-bold mb-3">Ticket Activity</h5>
                @forelse($ticket->comments as $comment)
                    <div class="border-bottom py-3"><div class="d-flex justify-content-between gap-3"><strong>{{ $comment->user?->name ?? 'System' }}</strong><small class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small></div><div class="mt-1" style="white-space:pre-wrap">{{ $comment->message }}</div></div>
                @empty
                    <p class="text-muted">No activity yet.</p>
                @endforelse

                @if($ticket->status !== 'Closed')
                <form method="POST" action="{{ route('employee.it-support.comments.store', $ticket) }}" class="mt-4">@csrf
                    <label class="form-label">Add Comment</label><textarea name="message" rows="3" required class="form-control mb-2"></textarea><button class="btn btn-primary">Add Comment</button>
                </form>
                @endif
            </div></div>
        </div>
        <div class="col-lg-4"><div class="card"><div class="card-body p-4"><h5 class="fw-bold">Support Summary</h5><p class="mb-2"><strong>Employee:</strong> {{ $ticket->employee?->full_name }}</p><p class="mb-2"><strong>Employee ID:</strong> {{ $ticket->employee?->employee_id }}</p><p class="mb-0"><strong>Last Updated:</strong> {{ $ticket->updated_at->format('d M Y H:i') }}</p></div></div></div>
    </div>
</div>
@endsection
