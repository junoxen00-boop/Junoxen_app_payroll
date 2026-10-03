@extends('layouts.admin')

@section('title','My Profile')

@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body text-center py-5">

                    <div class="mx-auto mb-3"
                         style="
                            width:100px;
                            height:100px;
                            border-radius:50%;
                            background:linear-gradient(135deg,#4f46e5,#2563eb);
                            color:white;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:40px;
                            font-weight:bold;
                         ">

                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}

                    </div>

                    <h3 class="fw-bold mb-1">
                        {{ auth()->user()->name }}
                    </h3>

                    <p class="text-muted mb-3">
                        {{ auth()->user()->role->name }}
                    </p>

                    <span class="badge bg-success px-3 py-2">
                        Active Employee
                    </span>

                </div>

            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Profile Information
                    </h5>

                </div>

                <div class="card-body">

                    <div class="row gy-4">

                        <div class="col-md-6">

                            <label class="text-muted">
                                Full Name
                            </label>

                            <h5>
                                {{ auth()->user()->name }}
                            </h5>

                        </div>

                        <div class="col-md-6">

                            <label class="text-muted">
                                Email Address
                            </label>

                            <h5>
                                {{ auth()->user()->email }}
                            </h5>

                        </div>

                        <div class="col-md-6">

                            <label class="text-muted">
                                Role
                            </label>

                            <h5>
                                {{ auth()->user()->role->name }}
                            </h5>

                        </div>

                        <div class="col-md-6">

                            <label class="text-muted">
                                Department
                            </label>

                            <h5>

                                {{ optional(auth()->user()->employee->department)->name ?? 'Not Assigned' }}

                            </h5>

                        </div>

                        <div class="col-md-6">

                            <label class="text-muted">
                                Employee ID
                            </label>

                            <h5>

                                {{ auth()->user()->employee->employee_id ?? auth()->user()->employee->id }}

                            </h5>

                        </div>

                        <div class="col-md-6">

                            <label class="text-muted">
                                Status
                            </label>

                            <h5>

                                {{ auth()->user()->employee->status ?? 'Active' }}

                            </h5>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection