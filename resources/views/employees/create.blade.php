@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Create Employee</h1>
            <p class="text-muted mb-0">Add a new employee to your Employee Dashboard.</p>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            Back to Employees
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            @if ($departments->isEmpty())
                <div class="alert alert-warning">
                    Please create at least one department before adding employees.
                    <a href="{{ route('departments.create') }}" class="alert-link">Create Department</a>
                </div>
            @endif

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_id" class="form-label">Employee ID</label>
                        <input
                            type="text"
                            id="employee_id"
                            class="form-control"
                            value="Auto-generated after save"
                            disabled
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            value="{{ old('full_name') }}"
                            class="form-control @error('full_name') is-invalid @enderror"
                            placeholder="Example: John Smith"
                            autofocus
                        >
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="example@email.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="mobile_number" class="form-label">
                            Mobile Number <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="mobile_number"
                            id="mobile_number"
                            value="{{ old('mobile_number') }}"
                            class="form-control @error('mobile_number') is-invalid @enderror"
                            placeholder="+91 98765 43210"
                        >
                        @error('mobile_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label">
                            Department <span class="text-danger">*</span>
                        </label>
                        <select
                            name="department_id"
                            id="department_id"
                            class="form-select @error('department_id') is-invalid @enderror"
                        >
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option
                                    value="{{ $department->id }}"
                                    @selected(old('department_id') == $department->id)
                                >
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="designation" class="form-label">
                            Designation <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="designation"
                            id="designation"
                            value="{{ old('designation') }}"
                            class="form-control @error('designation') is-invalid @enderror"
                            placeholder="Example: HR Manager"
                        >
                        @error('designation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="joining_date" class="form-label">
                            Joining Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            name="joining_date"
                            id="joining_date"
                            value="{{ old('joining_date') }}"
                            class="form-control @error('joining_date') is-invalid @enderror"
                        >
                        @error('joining_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="status" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                        >
                            <option value="Active" @selected(old('status', 'Active') === 'Active')>
                                Active
                            </option>
                            <option value="Inactive" @selected(old('status') === 'Inactive')>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary" @disabled($departments->isEmpty())>
                        Save Employee
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection