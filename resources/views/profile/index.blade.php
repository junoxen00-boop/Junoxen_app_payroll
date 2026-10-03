@extends('layouts.admin')



@section('content')

<div class="container-fluid">

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            Profile updated successfully.
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-person-circle"></i>

                My Profile

            </h4>

        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('profile.update') }}">

                @csrf
                @method('PATCH')

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Full Name

                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $user->name) }}"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Email Address

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $user->email) }}"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Role

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $user->role->name }}"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Employee ID

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ optional($user->employee)->employee_id }}"
                            readonly>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-check-circle"></i>

                    Save Profile

                </button>

            </form>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-key"></i>

                Change Password

            </h4>

        </div>

        <div class="card-body">

                    <form method="POST"
                  action="{{ route('password.update') }}">

                @csrf

                <div class="row">

                    <div class="col-md-12 mb-4">

                        <label class="form-label">

                            Current Password

                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            New Password

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Confirm Password

                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-key-fill"></i>

                    Update Password

                </button>

            </form>

        </div>

    </div>

</div>

@endsection