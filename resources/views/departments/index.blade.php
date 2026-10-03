@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Departments</h1>
            <p class="text-muted mb-0">Manage company departments from here.</p>
        </div>

        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            + Add Department
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
            <form action="{{ route('departments.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Search department by name or description..."
                    >
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        Search
                    </button>
                </div>

                @if ($search !== '')
                    <div class="col-12 mt-2">
                        <a href="{{ route('departments.index') }}" class="small text-decoration-none">
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
                            <th style="width: 80px;">#</th>
                            <th>Department Name</th>
                            <th>Description</th>
                            <th style="width: 180px;" class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td>
                                    {{ $departments->firstItem() + $loop->index }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $department->name }}
                                </td>

                                <td class="text-muted">
                                    {{ $department->description ?: '—' }}
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a
                                            href="{{ route('departments.edit', $department) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('departments.destroy', $department) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this department?');"
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
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        No departments found.
                                    </div>

                                    @if ($search !== '')
                                        <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                                            View all departments
                                        </a>
                                    @else
                                        <a href="{{ route('departments.create') }}" class="btn btn-sm btn-primary mt-3">
                                            Create first department
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if ($departments->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    {{ $departments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection