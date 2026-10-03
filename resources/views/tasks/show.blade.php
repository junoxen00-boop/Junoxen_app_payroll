@extends('layouts.admin')

@section('content')

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">Task Details</h2>
        <p class="text-muted mb-0">
            View complete information about this task.
        </p>
    </div>

    <div>

        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
            Back
        </a>

        <a href="{{ route('tasks.edit',$task) }}" class="btn btn-primary">
            Edit
        </a>

    </div>

</div>

<div class="row">

    <div class="col-lg-8">

        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Task Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="text-muted">
                            Task Code
                        </label>

                        <h6>
                            {{ $task->task_code }}
                        </h6>

                    </div>

                    <div class="col-md-6">

                        <label class="text-muted">
                            Priority
                        </label>

                        <br>

                        @switch($task->priority)

                            @case('Critical')
                                <span class="badge bg-dark">
                                    Critical
                                </span>
                            @break

                            @case('High')
                                <span class="badge bg-danger">
                                    High
                                </span>
                            @break

                            @case('Medium')
                                <span class="badge bg-warning text-dark">
                                    Medium
                                </span>
                            @break

                            @default
                                <span class="badge bg-info">
                                    Low
                                </span>

                        @endswitch

                    </div>

                </div>

                <div class="mb-3">

                    <label class="text-muted">
                        Title
                    </label>

                    <h5>
                        {{ $task->title }}
                    </h5>

                </div>

                <div class="mb-3">

                    <label class="text-muted">
                        Description
                    </label>

                    <div class="border rounded p-3 bg-light">

                        {{ $task->description ?: 'No description available.' }}

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <label class="text-muted">
                            Start Date
                        </label>

                        <h6>
                            {{ $task->start_date?->format('d M Y') }}
                        </h6>

                    </div>

                    <div class="col-md-6">

                        <label class="text-muted">
                            Due Date
                        </label>

                        <h6>
                            {{ $task->due_date?->format('d M Y') }}
                        </h6>

                    </div>

                </div>

            </div>

        </div>

            </div>

    <div class="col-lg-4">

        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">
                    Assigned Employees
                </h5>
            </div>

            <div class="card-body">

                @forelse($task->assignments as $assignment)

                    <div class="border rounded p-3 mb-3">

                        <h6 class="mb-1">
                            {{ $assignment->employee->full_name }}
                        </h6>

                        <div class="text-muted small mb-2">
                            {{ $assignment->employee->employee_id }}
                        </div>

                        <div class="mb-2">

                            <strong>Department:</strong>

                            {{ $assignment->employee->department?->name ?? '-' }}

                        </div>

                        <div class="mb-2">

                            <strong>Status:</strong>

                            @php
                                $badge = match($assignment->status){
                                    'Completed' => 'success',
                                    'In Progress' => 'primary',
                                    default => 'secondary'
                                };
                            @endphp

                            <span class="badge bg-{{ $badge }}">
                                {{ $assignment->status }}
                            </span>

                        </div>

                        <div class="mb-2">

                            <strong>Progress:</strong>

                            {{ $assignment->progress }}%

                        </div>

                        @if($assignment->remarks)

                            <div class="mb-2">

                                <strong>Remarks:</strong>

                                <div class="text-muted">
                                    {{ $assignment->remarks }}
                                </div>

                            </div>

                        @endif

                        @if($assignment->completed_at)

                            <div>

                                <strong>Completed:</strong>

                                {{ $assignment->completed_at->format('d M Y h:i A') }}

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="alert alert-warning mb-0">

                        No employee assigned.

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

<div class="card shadow-sm mt-4">

    <div class="card-header">
        <h5 class="mb-0">
            Task Summary
        </h5>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="text-muted">
                    Created By
                </label>

                <h6>
                    {{ $task->creator?->name ?? 'System' }}
                </h6>

            </div>

            <div class="col-md-6 mb-3">

                <label class="text-muted">
                    Created Date
                </label>

                <h6>
                    {{ $task->created_at?->format('d M Y h:i A') }}
                </h6>

            </div>

            <div class="col-md-6 mb-3">

                <label class="text-muted">
                    Last Updated
                </label>

                <h6>
                    {{ $task->updated_at?->format('d M Y h:i A') }}
                </h6>

            </div>

            <div class="col-md-6 mb-3">

                <label class="text-muted">
                    Total Assigned Employees
                </label>

                <h6>
                    {{ $task->assignments->count() }}
                </h6>

            </div>

        </div>

    </div>

</div>

</div>

@endsection