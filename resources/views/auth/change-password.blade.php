@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Change Password
                    </h4>
                </div>

                <div class="card-body">

                    <div class="alert alert-warning">

                        <strong>First Login</strong>

                        <br><br>

                        For security reasons you must change your temporary password before continuing.

                    </div>

                    <form method="POST" action="{{ route('password.update') }}">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">

                                Current Password

                            </label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                required
                            >

                            @error('current_password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                New Password

                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="mb-4">

                            <label class="form-label">

                                Confirm Password

                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >

                        </div>

                        <button
                            class="btn btn-primary w-100">

                            Update Password

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card shadow-sm">

    <div class="card-header">
        <h3 class="mb-0">
            Change Password
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ route('password.update') }}"
              method="POST">

            @csrf

            <div class="mb-4">

                <label class="form-label">
                    Current Password
                </label>

                <input
                    type="password"
                    name="current_password"
                    class="form-control"
                    required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    New Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                <i class="bi bi-key"></i>

                Update Password

            </button>

        </form>

    </div>

</div>

@endsection