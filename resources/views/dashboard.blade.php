@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- Welcome Banner -->
    <div class="card border-0 mb-4" style="background:linear-gradient(135deg,#2563eb,#4f46e5);border-radius:20px;color:#fff;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2">
                        Welcome, {{ auth()->user()->name }} 👋
                    </h2>

                    <p class="mb-0 opacity-75">
                        Employee Task Management Dashboard
                    </p>
                </div>

                <div class="text-end">
                    <h5>{{ now()->format('d M Y') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4">

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Departments</small>
                        <h2 class="fw-bold">{{ $departments }}</h2>
                    </div>

                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-building text-primary fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Employees</small>
                        <h2 class="fw-bold">{{ $employees }}</h2>
                    </div>

                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-people-fill text-success fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Tasks</small>
                        <h2 class="fw-bold">{{ $tasks }}</h2>
                    </div>

                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-list-task text-warning fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">System Status</small>
                        <h2 class="fw-bold text-success">Online</h2>
                    </div>

                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-check-circle-fill text-success fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Dashboard Panels -->
    <div class="row mt-4">

        <div class="col-lg-8">

            <div class="card mb-4">

                <div class="card-header bg-white border-0 fw-bold">
                    Recent Tasks
                </div>

                <div class="card-body">

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>

                    <th>Task Code</th>

                    <th>Title</th>

                    <th>Employees</th>

                    <th>Priority</th>

                    <th>Due Date</th>

                </tr>

            </thead>

            <tbody>

                @forelse($recentTasks as $task)

                    <tr>

                        <td>
                            <strong>{{ $task->task_code }}</strong>
                        </td>

                        <td>
                            {{ $task->title }}
                        </td>

                        <td>

                            @forelse($task->assignments as $assignment)

                                <span class="badge bg-secondary me-1 mb-1">

                                    {{ $assignment->employee->full_name }}

                                </span>

                            @empty

                                <span class="text-muted">
                                    Not Assigned
                                </span>

                            @endforelse

                        </td>

                        <td>

                            @if($task->priority=='Critical')

                                <span class="badge bg-danger">Critical</span>

                            @elseif($task->priority=='High')

                                <span class="badge bg-warning text-dark">High</span>

                            @elseif($task->priority=='Medium')

                                <span class="badge bg-primary">Medium</span>

                            @else

                                <span class="badge bg-success">Low</span>

                            @endif

                        </td>

                        <td>

                            {{ $task->due_date?->format('d M Y') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center">

                            No Tasks Found

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card mb-4">

                <div class="card-header bg-white border-0 fw-bold">
                    Quick Actions
                </div>

                <div class="card-body d-grid gap-3">

                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <i class="bi bi-building-add"></i>
                        Add Department
                    </a>

                    <a href="{{ route('employees.create') }}" class="btn btn-success">
                        <i class="bi bi-person-plus"></i>
                        Add Employee
                    </a>

                    <a href="{{ route('tasks.create') }}" class="btn btn-warning text-white">
                        <i class="bi bi-plus-circle"></i>
                        Create Task
                    </a>

                </div>

            </div>

            <div class="card">

                <div class="card-header bg-white border-0 fw-bold">
                    Upcoming Deadlines
                </div>

                <div class="card-body text-center py-5 text-muted">

                    <i class="bi bi-calendar-event fs-1"></i>

                    <h6 class="mt-3">
                        No upcoming deadlines
                    </h6>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection