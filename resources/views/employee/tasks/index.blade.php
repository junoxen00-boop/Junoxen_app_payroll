@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                My Tasks

            </h2>

            <p class="text-muted mb-0">

                View and update all your assigned tasks.

            </p>

        </div>

    </div>

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    <form method="GET" action="{{ route('employee.tasks.index') }}" class="mb-4">

    <div class="row g-3">

        <div class="col-md-4">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search Task..."
                value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <select name="priority" class="form-select">
                <option value="">All Priority</option>
                <option value="High" {{ request('priority')=='High' ? 'selected' : '' }}>🔴 High</option>
                <option value="Medium" {{ request('priority')=='Medium' ? 'selected' : '' }}>🟡 Medium</option>
                <option value="Low" {{ request('priority')=='Low' ? 'selected' : '' }}>🟢 Low</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status')=='Pending' ? 'selected' : '' }}>Pending</option>
                <option value="In Progress" {{ request('status')=='In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Completed" {{ request('status')=='Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
        </div>

    </div>

</form>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                Assigned Tasks

            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="70">S.No</th>

                            <th width="120">

                                Task Code

                            </th>

                            <th>

                                Title

                            </th>

                            <th width="120">

                                Priority

                            </th>

                            <th width="150">

                                Status

                            </th>

                            <th width="170">

                                Progress

                            </th>

                            <th width="140">

                                Due Date

                            </th>

                            <th width="110">

                                Action

                            </th>

                        </tr>

                    </thead>
<tbody>

@forelse($assignments as $index => $assignment)

<tr>

    <td>
        {{ $index + 1 }}
    </td>

    <td>
        {{ $assignment->task->task_code ?? '-' }}
    </td>

    <td>

        <div class="fw-semibold">
            {{ $assignment->task->title ?? 'No Task' }}
        </div>

        <small class="text-muted">
            Assigned by
            {{ $assignment->task->creator->name ?? 'Admin' }}
        </small>

    </td>

    <td>

        @switch($assignment->task->priority ?? '')

            @case('Critical')
                <span class="badge bg-danger">Critical</span>
                @break

            @case('High')
<span class="badge bg-danger">High</span>
                @break

           @case('Medium')
<span class="badge bg-warning text-dark">Medium</span>
                @break

            @default
                <span class="badge bg-success">Low</span>

        @endswitch

    </td>

    <td>

        @switch($assignment->status)

            @case('Pending')
                <span class="badge bg-secondary">Pending</span>
                @break

            @case('In Progress')
                <span class="badge bg-warning text-dark">In Progress</span>
                @break

            @case('Completed')
                <span class="badge bg-success">Completed</span>
                @break

            @default
                <span class="badge bg-secondary">
                    {{ $assignment->status }}
                </span>

        @endswitch

    </td>

    <td>

        <div class="progress" style="height:20px;">

            <div
                class="progress-bar
                @if($assignment->progress >= 100)
                    bg-success
                @elseif($assignment->progress >= 50)
                    bg-warning
                @else
                    bg-primary
                @endif"
                style="width: {{ $assignment->progress }}%;">

                {{ $assignment->progress }}%

            </div>

        </div>

    </td>

    <td>

        @if($assignment->task && $assignment->task->due_date)
            {{ \Carbon\Carbon::parse($assignment->task->due_date)->format('d M Y') }}
        @else
            -
        @endif

    </td>

    <td>

        <a
            href="{{ route('employee.tasks.show', $assignment) }}"
            class="btn btn-sm btn-primary">

            <i class="bi bi-eye"></i>

            View

        </a>

    </td>

</tr>

@empty

<tr>

    <td colspan="8" class="text-center py-5">

        <h5>No Tasks Assigned</h5>

    </td>

</tr>

@endforelse

</tbody>

                </table>

            </div>

        </div>

        {{-- 
@if($assignments->hasPages())

    <div class="card-footer">

        {{ $assignments->links() }}

    </div>

@endif
--}}

    </div>

</div>

@endsection