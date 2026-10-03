@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Edit Task</h1>
            <p class="text-muted mb-0">
                Update task details, priority, due date, and assigned employees.
            </p>
        </div>

        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
            Back to Tasks
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Task Code
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $task->task_code }}"
                            disabled
                        >
                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Assign To Employee
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="employees[]"
                            id="employees"
                            class="form-select @error('employees') is-invalid @enderror"
                            multiple
                            size="5"
                        >

                            @foreach($employees as $employee)

                                <option
                                    value="{{ $employee->id }}"
                                    @selected(in_array($employee->id, old('employees', $selectedEmployees)))
                                >

                                    {{ $employee->full_name }}

                                    @if($employee->employee_id)
                                        - {{ $employee->employee_id }}
                                    @endif

                                    @if($employee->department)
                                        - {{ $employee->department->name }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted">
                            Hold Ctrl (Windows) or Cmd (Mac) to select multiple employees.
                        </small>

                        @error('employees')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Task Title
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $task->title) }}"
                            class="form-control @error('title') is-invalid @enderror"
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                        >{{ old('description', $task->description) }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                                        <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Priority
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="priority"
                            class="form-select @error('priority') is-invalid @enderror"
                        >

                            <option value="Low"
                                @selected(old('priority',$task->priority)=='Low')>
                                Low
                            </option>

                            <option value="Medium"
                                @selected(old('priority',$task->priority)=='Medium')>
                                Medium
                            </option>

                            <option value="High"
                                @selected(old('priority',$task->priority)=='High')>
                                High
                            </option>

                            <option value="Critical"
                                @selected(old('priority',$task->priority)=='Critical')>
                                Critical
                            </option>

                        </select>

                        @error('priority')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Start Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            value="{{ old('start_date',$task->start_date?->format('Y-m-d')) }}"
                            class="form-control @error('start_date') is-invalid @enderror"
                        >

                        @error('start_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Due Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="due_date"
                            value="{{ old('due_date',$task->due_date?->format('Y-m-d')) }}"
                            class="form-control @error('due_date') is-invalid @enderror"
                        >

                        @error('due_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('tasks.index') }}"
                        class="btn btn-light">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Update Task
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection