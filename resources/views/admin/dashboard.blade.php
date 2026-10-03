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

                        Employee Task Management Dashboard

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

    {{-- Dashboard Statistics --}}

    <div class="row g-4">

        {{-- Departments --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">

                        Departments

                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold">

                            {{ $departments }}

                        </h2>

                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">

                            <i class="bi bi-building fs-2 text-primary"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Employees --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">

                        Employees

                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold">

                            {{ $employees }}

                        </h2>

                        <div class="bg-success bg-opacity-10 rounded-circle p-3">

                            <i class="bi bi-people-fill fs-2 text-success"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Tasks --}}

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">

                        Total Tasks

                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold">

                            {{ $tasks }}

                        </h2>

                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">

                            <i class="bi bi-list-task fs-2 text-warning"></i>

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

                        Pending Tasks

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

        <div class="col-xl-4 col-md-6">

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

        <div class="col-xl-4 col-md-6">

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

        {{-- Overdue --}}

        <div class="col-xl-4 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">

                        Overdue

                    </small>

                    <div class="d-flex justify-content-between align-items-center mt-3">

                        <h2 class="fw-bold text-danger">

                            {{ $overdueTasks }}

                        </h2>

                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">

                            <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Recent Tasks --}}

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-white">

            <h5 class="mb-0 fw-bold">

                Recent Tasks

            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Task Code</th>

                            <th>Title</th>

                            <th>Assigned To</th>

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

                                    @foreach($task->assignments as $assignment)

                                        <span class="badge bg-secondary me-1">

                                            {{ $assignment->employee->name }}

                                        </span>

                                    @endforeach

                                </td>

                                <td>

                                    @if($task->priority=='High')

                                        <span class="badge bg-danger">

                                            High

                                        </span>

                                    @elseif($task->priority=='Medium')

                                        <span class="badge bg-warning text-dark">

                                            Medium

                                        </span>

                                    @else

                                        <span class="badge bg-success">

                                            Low

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center py-4">

                                    No Tasks Found

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

        <div class="row mt-4">

        {{-- Quick Actions --}}
        <div class="col-lg-4 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        Quick Actions
                    </h5>
                </div>

                <div class="card-body d-grid gap-3">

                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        Add Employee
                    </a>

                    <a href="{{ route('tasks.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Create Task
                    </a>

                    <a href="{{ route('departments.create') }}" class="btn btn-warning text-dark">
                        <i class="bi bi-building-add me-2"></i>
                        Add Department
                    </a>

                </div>

            </div>

        </div>

        {{-- Upcoming Deadlines --}}
        <div class="col-lg-4 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        Upcoming Deadlines
                    </h5>
                </div>

                <div class="card-body">

                    @forelse($upcomingDeadlines as $task)

                        <div class="mb-3 pb-3 border-bottom">

                            <div class="fw-semibold">

                                {{ $task->title }}

                            </div>

                            <small class="text-muted">

                                Due :
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}

                            </small>

                        </div>

                    @empty

                        <p class="text-muted mb-0">

                            No upcoming deadlines.

                        </p>

                    @endforelse

                </div>

            </div>

        </div>

        {{-- Recent Employees --}}
        <div class="col-lg-4 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="mb-0 fw-bold">

                        Recent Employees

                    </h5>

                </div>

                <div class="card-body">

                    @forelse($recentEmployees as $employee)

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>

                                <div class="fw-semibold">

                                    {{ $employee->name }}

                                </div>

                                <small class="text-muted">

                                    {{ optional($employee->department)->name ?? 'No Department' }}

                                </small>

                            </div>

                            <span class="badge bg-primary">

                                New

                            </span>

                        </div>

                    @empty

                        <p class="text-muted mb-0">

                            No employees found.

                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection