@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- Welcome Banner --}}
    <div class="card border-0 shadow-sm mb-4"
         style="background:linear-gradient(135deg,#2563eb,#4f46e5);border-radius:20px;color:white;">

        <div class="card-body p-4">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <h2 class="fw-bold mb-2">
                        Welcome Back,
                        {{ auth()->user()->name }} 👋
                    </h2>

                    <p class="mb-0 opacity-75">
                        My Task Dashboard
                    </p>

                </div>

                <div class="col-lg-4 text-end">

                    <h5 class="mb-0">
                        {{ now()->format('d M Y') }}
                    </h5>

                    <small>
                        {{ now()->format('l') }}
                    </small>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4">

        {{-- Assigned Tasks --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Assigned Tasks
                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold">
                            {{ $assignedTasks }}
                        </h2>

                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-list-task fs-2 text-primary"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Pending --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Pending
                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold text-warning">
                            {{ $pendingTasks }}
                        </h2>

                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- In Progress --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        In Progress
                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold text-primary">
                            {{ $inProgressTasks }}
                        </h2>

                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-arrow-repeat fs-2 text-primary"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Completed --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Completed
                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold text-success">
                            {{ $completedTasks }}
                        </h2>

                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

                {{-- Completion --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Completion
                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold text-success">
                            {{ $completion }}%
                        </h2>

                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-graph-up-arrow fs-2 text-success"></i>
                        </div>

                    </div>

                    <div class="progress mt-3" style="height:8px;">

                        <div class="progress-bar bg-success"
                             role="progressbar"
                             style="width: {{ $completion }}%;"
                             aria-valuenow="{{ $completion }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- My Recent Tasks --}}
    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-white">

            <h5 class="mb-0 fw-bold">
                My Recent Tasks
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Task Code</th>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Due Date</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($recentTasks as $assignment)

                        <tr>

                            <td>
                                {{ $assignment->task->task_code }}
                            </td>

                           <td>
    <a href="{{ route('employee.tasks.show', $assignment) }}"
       class="fw-semibold text-decoration-none">
        {{ $assignment->task->title }}
    </a>
</td>

                            <td>

                                @if($assignment->status=='Pending')

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @elseif($assignment->status=='In Progress')

                                    <span class="badge bg-primary">
                                        In Progress
                                    </span>

                                @else

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ $assignment->progress }}%

                            </td>

                            <td>

                                {{ \Carbon\Carbon::parse($assignment->task->due_date)->format('d M Y') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-4">

                                No Assigned Tasks

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection