@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Tasks</h1>
            <p class="text-muted mb-0">Manage employee tasks, priorities, due dates, and progress.</p>
        </div>

        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            + Add Task
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form action="{{ route('tasks.index') }}" method="GET">

            <div class="row g-3">

                <div class="col-lg-3">

                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Search Task...">

                </div>

                <div class="col-lg-2">

                    <select
    id="department"
    name="department"
    class="form-select">

                        <option value="">
                            All Departments
                        </option>

                        @foreach($departments as $dept)

                            <option
                                value="{{ $dept->id }}"
                                {{ $department == $dept->id ? 'selected' : '' }}>

                                {{ $dept->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2">

                   <select
    id="employee"
    name="employee"
    class="form-select">

    <option value="">
        All Employees
    </option>

    @foreach($employees as $emp)

        <option
            value="{{ $emp->id }}"
            data-department="{{ $emp->department_id }}"
            {{ $employee == $emp->id ? 'selected' : '' }}>

            {{ $emp->full_name }}

        </option>

    @endforeach

</select>

                </div>

                <div class="col-lg-2">

                    <select
                        name="priority"
                        class="form-select">

                        <option value="">
                            All Priority
                        </option>

                        <option value="High" {{ $priority=='High' ? 'selected':'' }}>
                            🔴 High
                        </option>

                        <option value="Medium" {{ $priority=='Medium' ? 'selected':'' }}>
                            🟡 Medium
                        </option>

                        <option value="Low" {{ $priority=='Low' ? 'selected':'' }}>
                            🟢 Low
                        </option>

                        <option value="Critical" {{ $priority=='Critical' ? 'selected':'' }}>
                            ⚫ Critical
                        </option>

                    </select>

                </div>

                <div class="col-lg-2">

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            All Status
                        </option>

                        <option value="Pending" {{ $status=='Pending' ? 'selected':'' }}>
                            Pending
                        </option>

                        <option value="In Progress" {{ $status=='In Progress' ? 'selected':'' }}>
                            In Progress
                        </option>

                        <option value="Completed" {{ $status=='Completed' ? 'selected':'' }}>
                            Completed
                        </option>

                    </select>

                </div>

                <div class="col-lg-1 d-grid">

                    <button
                        class="btn btn-primary">

                        Search

                    </button>

                </div>

                <div class="col-lg-1 d-grid">

                    <a
                        href="{{ route('tasks.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Task ID</th>
                            <th>Title</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Priority</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th style="width: 190px;" class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td>
                                    {{ $tasks->firstItem() + $loop->index }}
                                </td>

                                <td class="fw-semibold">
                                   {{ $task->task_code ?? ('TASK-'.$task->id) }}
                                </td>

                                <td>
                                    <a
                                        href="{{ route('tasks.show', $task) }}"
                                        class="fw-semibold text-decoration-none"
                                    >
                                        {{ $task->title }}
                                    </a>

                                    @if ($task->description)
                                        <div class="small text-muted">
                                            {{ \Illuminate\Support\Str::limit($task->description, 60) }}
                                        </div>
                                    @endif
                                </td>
                              <td>
                              @forelse($task->employees as $employee)
                              <div>
                             <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">
                             {{ $employee->full_name }}
                             </a>

                             <small class="text-muted d-block">
                             {{ $employee->employee_id }}
                             </small>
                             </div>
                             @empty
                               —
                             @endforelse
                              </td>

                                <td>
                                 @forelse($task->employees as $employee)
                                      <div>{{ $employee->department->name ?? '-' }}</div>
                                       @empty
                                         —
                                      @endforelse
                                      </td>

                                <td>
                    @switch($task->priority)

                     @case('Critical')
                     <span class="badge bg-dark">Critical</span>
                     @break

                     @case('High')
                     <span class="badge bg-danger">High</span>
                     @break

                     @case('Medium')
                     <span class="badge bg-warning text-dark">Medium</span>
                     @break 

                      @default
                      <span class="badge bg-info text-dark">Low</span>

                      @endswitch
                                </td>

                                <td>
                                    {{ $task->start_date?->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $task->due_date?->format('d M Y') }}
                                </td>

                             <td>

@forelse($task->assignments as $assignment)

    @php
        $badge = match($assignment->status){
            'Completed' => 'success',
            'In Progress' => 'primary',
            default => 'secondary'
        };
    @endphp

    <div class="mb-1">
        <span class="badge bg-{{ $badge }}">
            {{ $assignment->employee->full_name }}
            -
            {{ $assignment->status }}
        </span>
    </div>

@empty
    <span class="badge bg-secondary">Pending</span>
@endforelse

</td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a
                                            href="{{ route('tasks.show', $task) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route('tasks.edit', $task) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('tasks.destroy', $task) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this task?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="text-muted">
                                        No tasks found.
                                    </div>

                                    @if ($search !== '')
                                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                                            View all tasks
                                        </a>
                                    @else
                                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary mt-3">
                                            Create first task
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if ($tasks->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    {{ $tasks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    const department = document.getElementById('department');
    const employee = document.getElementById('employee');

    const options = Array.from(employee.options);

    department.addEventListener('change', function () {

        const departmentId = this.value;

        employee.innerHTML = '';

        employee.appendChild(options[0]); // All Employees

        options.slice(1).forEach(function (option) {

            if (
                departmentId === '' ||
                option.dataset.department === departmentId
            ) {
                employee.appendChild(option);
            }

        });

        employee.value = '';
    });

});
</script>