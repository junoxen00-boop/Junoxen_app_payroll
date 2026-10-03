@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-lg-8 mx-auto">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Import Monthly To-Do List
                    </h4>
                </div>

                <div class="card-body">

                @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
@endif

                @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                   <form action="{{ route('imports.preview') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Department
                            </label>

                            <select class="form-select" name="department_id" required>
                                <option value=""> Select Department </option>

                                @foreach(\App\Models\Department::orderBy('name')->get() as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Employee
                            </label>

                            <select class="form-select" name="employee_id" required>

                                <option value="">
                                     Select Employee 
                                </option>

                                @foreach($employees as $employee)

                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Month
                            </label>

                            <select
    name="month"
    class="form-select"
    required>

    <option value=""> Select Month </option>

    <option value="2026-01">January 2026</option>
    <option value="2026-02">February 2026</option>
    <option value="2026-03">March 2026</option>
    <option value="2026-04">April 2026</option>
    <option value="2026-05">May 2026</option>
    <option value="2026-06">June 2026</option>
    <option value="2026-07">July 2026</option>
    <option value="2026-08">August 2026</option>
    <option value="2026-09">September 2026</option>
    <option value="2026-10">October 2026</option>
    <option value="2026-11">November 2026</option>
    <option value="2026-12">December 2026</option>

</select>

                        </div>

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Upload Excel / CSV File
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="file"
                                accept=".xlsx,.xls,.csv"
                                required>

                            <small class="text-muted">
                                Supported formats:
                                .xlsx, .xls and .csv
                            </small>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-upload"></i>

                            Upload & Preview

                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

@endsection