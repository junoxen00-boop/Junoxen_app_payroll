@extends('layouts.admin', [
    'title' => $title,
    'pageTitle' => $title
])

@section('content')
<div class="card stat-card">
    <div class="card-body p-5">
        <h3 class="fw-bold mb-2">{{ $title }}</h3>
        <p class="text-muted mb-0">{{ $description }}</p>
    </div>
</div>
@endsection