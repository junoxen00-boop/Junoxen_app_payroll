@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="fw-bold mb-1">Raise IT Support Ticket</h3><p class="text-muted mb-0">Describe the issue clearly so the IT team can assist you.</p></div>
        <a href="{{ route('employee.it-support.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="card"><div class="card-body p-4">
        <div class="mb-4 p-3 bg-light rounded-3">
            <strong>{{ auth()->user()->employee?->full_name }}</strong><br>
            <span class="text-muted">{{ auth()->user()->employee?->department?->name }} · {{ auth()->user()->employee?->employee_id }}</span>
        </div>
        <form method="POST" action="{{ route('employee.it-support.store') }}" class="row g-3">
            @csrf
            <div class="col-12"><label class="form-label">Subject *</label><input name="subject" maxlength="180" required class="form-control" value="{{ old('subject') }}"></div>
            <div class="col-md-6"><label class="form-label">Category *</label><select name="category" required class="form-select"><option value="">Select category</option>@foreach(\App\Models\ItSupportTicket::CATEGORIES as $category)<option value="{{ $category }}" @selected(old('category')===$category)>{{ $category }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Priority *</label><select name="priority" required class="form-select">@foreach(\App\Models\ItSupportTicket::PRIORITIES as $priority)<option value="{{ $priority }}" @selected(old('priority','Medium')===$priority)>{{ $priority }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">Description *</label><textarea name="description" rows="7" required class="form-control" placeholder="Explain what happened, any error message, device/application involved, and what you already tried.">{{ old('description') }}</textarea></div>
            <div class="col-12"><button class="btn btn-primary"><i class="bi bi-send me-1"></i> Submit Ticket</button></div>
        </form>
    </div></div>
</div>
@endsection
