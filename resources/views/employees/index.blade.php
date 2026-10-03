@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Employees</h1>
            <p class="text-muted mb-0">Manage employee records, departments, roles, and status.</p>
        </div>

        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            + Add Employee
        </a>
    </div>

   @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">

        <h5 class="mb-2">
            ✅ {{ session('success') }}
        </h5>

        @if(session('temporary_password'))

            <hr>

            <h6 class="fw-bold mb-3">
                Employee Login Details
            </h6>

            <table class="table table-sm table-borderless mb-3">
                <tr>
                    <th width="180">Email</th>
                    <td>{{ session('employee_email') }}</td>
                </tr>

                <tr>
                    <th>Temporary Password</th>
                    <td>
                        <span class="badge bg-dark fs-6">
                            {{ session('temporary_password') }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="alert alert-warning mb-0">
                <strong>Important:</strong>
                Copy this temporary password now and share it with the employee.
                It will not be shown again.
            </div>

        @endif

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
        </button>

    </div>
@endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('employees.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Search by employee ID, name, email, mobile, department, designation, status, or joining date..."
                    >
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        Search
                    </button>
                </div>

                @if ($search !== '')
                    <div class="col-12 mt-2">
                        <a href="{{ route('employees.index') }}" class="small text-decoration-none">
                            Clear search
                        </a>
                    </div>
                @endif
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
                            <th>Employee ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                            <th style="width: 190px;" class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>
                                    {{ $employees->firstItem() + $loop->index }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $employee->employee_id }}
                                </td>

                                <td>
                                    <a
                                        href="{{ route('employees.show', $employee) }}"
                                        class="fw-semibold text-decoration-none"
                                    >
                                        {{ $employee->full_name }}
                                    </a>
                                </td>

                                <td>
                                    {{ $employee->email }}
                                </td>

                                <td>
                                    {{ $employee->mobile_number }}
                                </td>

                                <td>
                                    {{ $employee->department?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $employee->designation }}
                                </td>

                                <td>
                                    {{ $employee->joining_date?->format('d M Y') }}
                                </td>

                                <td>
                                    @if ($employee->status === 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a
                                            href="{{ route('employees.show', $employee) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route('employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

   

                                        <form
                                            action="{{ route('employees.destroy', $employee) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this employee?');"
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
                                        No employees found.
                                    </div>

                                    @if ($search !== '')
                                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                                            View all employees
                                        </a>
                                    @else
                                        <a href="{{ route('employees.create') }}" class="btn btn-sm btn-primary mt-3">
                                            Create first employee
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if ($employees->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection