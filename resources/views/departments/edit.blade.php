@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Edit Department</h1>
            <p class="text-muted mb-0">Update department details.</p>
        </div>

        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
            Back to Departments
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Department Name <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $department->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Example: Human Resources"
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Write a short description about this department..."
                    >{{ old('description', $department->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('departments.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Update Department
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection