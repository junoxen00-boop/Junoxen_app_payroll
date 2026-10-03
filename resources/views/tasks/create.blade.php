@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Create Task</h1>
            <p class="text-muted mb-0">Assign a new task to an employee.</p>
        </div>

        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
            Back to Tasks
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            @if ($employees->isEmpty())
                <div class="alert alert-warning">
                    Please create at least one active employee before assigning tasks.
                    <a href="{{ route('employees.create') }}" class="alert-link">Create Employee</a>
                </div>
            @endif

            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="task_id" class="form-label">Task ID</label>
                        <input
                            type="text"
                            id="task_id"
                            class="form-control"
                            value="Auto-generated after save"
                            disabled
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="employee_id" class="form-label">
                            Assign To Employee <span class="text-danger">*</span>
                        </label>

                        <select
                            name="employees[]"
                            id="employees"
                            multiple
                            class="form-select @error('employees') is-invalid @enderror"
                        >
                            
                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    @selected(in_array($employee->id, old('employees', [])))
                                >
                                    {{ $employee->full_name }}
                                    @if ($employee->employee_id)
                                        - {{ $employee->employee_id }}
                                    @endif
                                    @if ($employee->department)
                                        - {{ $employee->department->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('employees')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label">
                            Task Title <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Example: Prepare monthly employee report"
                            autofocus
                        >

                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Write task details, instructions, or notes..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label">
                            Priority <span class="text-danger">*</span>
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            class="form-select @error('priority') is-invalid @enderror"
                        >
                            <option value="Low" @selected(old('priority') === 'Low')>Low</option>
                            <option value="Medium" @selected(old('priority', 'Medium') === 'Medium')>Medium</option>
                            <option value="High" @selected(old('priority') === 'High')>High</option>
                        </select>

                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                        >
                            <option value="Pending" @selected(old('status', 'Pending') === 'Pending')>Pending</option>
                            <option value="In Progress" @selected(old('status') === 'In Progress')>In Progress</option>
                            <option value="Completed" @selected(old('status') === 'Completed')>Completed</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="start_date" class="form-label">
                            Start Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            id="start_date"
                            value="{{ old('start_date') }}"
                            class="form-control @error('start_date') is-invalid @enderror"
                        >

                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="due_date" class="form-label">
                            Due Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="due_date"
                            id="due_date"
                            value="{{ old('due_date') }}"
                            class="form-control @error('due_date') is-invalid @enderror"
                        >

                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tasks.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary" @disabled($employees->isEmpty())>
                        Save Task
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection